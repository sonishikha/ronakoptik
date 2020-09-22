<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Models\OrderItems;
use App\Models\ApiValidation;
use App\Customer;
use App\User;
use App\Product;
use Exception;


class OrderController extends Controller
{
    public function store(Request $request){
        try{
            $api_validation = new ApiValidation;
            $user = $api_validation->validateAndGetUser($request);
            if(empty($request->saleOrderWrapper)){
                throw New Exception('Please provide sale order data.');
            }
            $insert_array = $insert_item_arr = [];
            foreach($request->saleOrderWrapper as $sale_order){
                $customer_address = Customer::select('BP_Code__c','Address_Type__c','Address_Name__c')->where('BP_Code__c',$sale_order['account'])->get();
                $billing_address = $shipping_address = '';
                foreach($customer_address as $customer){
                    if($customer->Address_Type__c == 'B'){
                        $billing_address = $customer->Address_Name__c;
                    }
                    if($customer->Address_Type__c == 'S'){
                        $shipping_address = $customer->Address_Name__c;
                    }
                }
                $order = new Order;
                $order->creator_id = $user->id;
                $order->bp_code = $sale_order['account'];
                $order->tax_code = 'test';
                $order->local_id = $sale_order['local_id'];
                $order->billing_address = $billing_address;
                $order->shipping_address = $shipping_address;
                $order->cash_discount = $sale_order['Discount'];
                $order->comments = (empty($sale_order['Remarks'])) ? 'No Comments' : $sale_order['Remarks'];
                $result = $order->save();
                if($result){
                    if(empty($sale_order['saleOrdeLineItems'])){
                        throw New Exception('Sale order items not available.');
                    }
                    foreach($sale_order['saleOrdeLineItems'] as $item){
                        $product = Product::select('Item_Group_Code__c')->where('Item_Code__c',$item['ProductId'])->get();
                        $order_item = new OrderItems;
                        $order_item->tran_id = $order->id;
                        $order_item->group_code = $product[0]->Item_Group_Code__c;
                        $order_item->item_code = $item['ProductId'];
                        $order_item->quantity = $item['Quantity'];
                        $order_item->price = $item['Price'];
                        $order_item->discount = $item['Discount'];
                        $order_item->save();
                    }
                    return json_encode(['success'=>1]);
                }else{
                    throw New Exception('Cannot create order. Please try again.');
                }
            }
        }catch(Exception $e){
            return json_encode(['success'=>0, "message"=>$e->getMessage()]);
        }
    }

    public function getInvoiceList(Request $request){
        try{
            $api_validation = new ApiValidation;
            $user = $api_validation->validateAndGetUser($request, true);
           
            $order_data = Order::with(array('OrderItems'=>function($query){
                                    $query->select('tran_id','item_code','quantity','group_code','price','discount');
                                }))
                                ->select('id','bp_code as account_id','local_id','tax_code as taxCode','shipping_address as shipToParty','cash_discount as discount','comments as remakrs','ts as createdDate','status')
                                ->where('creator_id', $user->id)
                                ->orderBy('ts', 'desc')
                                ->paginate(100, ['*'], 'page', $request->offSet);
            if($order_data->count() == 0){
                throw New Exception('No Records Found.');
            }
            
            $customer_ids = $order_data->pluck('account_id');
            $customers = Customer::select('BP_Code__c','Customer_Name__c')
                                ->whereIn('BP_Code__c',$customer_ids)->get();
            $customers = $customers->pluck('Customer_Name__c','BP_Code__c');

            $invoice_array = [];
            foreach($order_data as $key=>$order){
                $order_data[$key]->invoice_id = $order->id;
                $order_data[$key]->customer_name = $customers[$order->account_id];
                $order_items = [];
                $item_ids = $order->OrderItems->pluck('item_code');
                $products = Product::select('Item_Code__c','Item_Name__c', 'Brand__c', 'Collection__c', 'Product__c')->whereIn('Item_Code__c',$item_ids)->get()->keyBy('Item_Code__c');
                foreach($order->OrderItems as $item){
                    $product = $products[$item->item_code];
                    $order_items[] = array(
                                'ProductId' => $item->item_code,
                                'ProductName' => $product->Item_Name__c,
                                'Brand' => $product->Brand__c,
                                'Collection' => $product->Collection__c,
                                'Category' => $product->Product__c,
                                'Quantity' => $item->quantity,
                                'group_code' => $item->group_code,
                                'Price' => $item->price,
                                'Discount' => $item->discount
                            );
                }
                $order_data[$key]->saleOrdeLineItems = $order_items;
            }
            return ['success'=>1] + $order_data->toArray();
        }catch(Exception $e){
            return json_encode(['success'=>0, "message"=>$e->getMessage()]);
        }      
    }
}
