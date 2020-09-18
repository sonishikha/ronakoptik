<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Model\OrderItems;
use App\Models\ApiValidation;
use App\Customer;
use App\User;
use App\Product;
use Exception;


class OrderController extends Controller
{
    public function store(Request $request){
        try{
            $creator_id = User::select('id')->where('email',$request->userName)->limit(1)->get();
            $creator_id = $creator_id[0]->id;
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
                $order->creator_id = $creator_id;
                $order->bp_code = $sale_order['account'];
                $order->tax_code = 'test';
                $order->local_id = $sale_order['local_id'];
                $order->billing_address = $billing_address;
                $order->shipping_address = $shipping_address;
                $order->cash_discount = $sale_order['Discount'];
                $order->comments = $sale_order['Remarks'];
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
                }else{
                    throw New Exception('Cannot create order. Please try again.');
                }
            }
            return json_encode(['success'=>1]);
        }catch(Exception $e){
            return json_encode(['success'=>0, "message"=>$e->getMessage()]);
        }
    }

    public function getInvoiceList(Request $request){
        try{
            $api_validation = new ApiValidation;
            $user = $api_validation->validateAndGetUser($request);
            
            $order_data = Order::select(
                                'id as invoice_id','bp_code as account_id','tax_code as taxCode',
                                'shipping_address as shipToParty','cash_discount as discount',
                                'comments as remarks','ts as createdDate','status')
                                ->where('creator_id', $user->id)
                                ->orderBy('ts', 'desc')
                                ->paginate(100, ['*'], 'page', $request->offSet);
            foreach($order_data as $key=>$order){
                $customer_address = Customer::select('Customer_Name__c')
                                    ->where('BP_Code__c',$order['account_id'])
                                    ->get();
                $order_data[$key]->customer_name = $customer_address[0]->Customer_Name__c;
                $items = OrderItems::where('tran_id',$order['invoice_id'])->get();
                $order_items = [];
                foreach($items as $item){
                    $product = Product::select('Item_Name__c', 'Brand__c', 'Collection__c', 'Product__c')->where('Item_Code__c',$item['item_code'])->get();
                    $order_items[] = array(
                        'ProductId' => $item['item_code'],
                        'ProductName' => $product[0]->Item_Name__c,
                        'Brand' => $product[0]->Brand__c,
                        'Collection' => $product[0]->Collection__c,
                        'Category' => $product[0]->Product__c,
                        'Quantity' => $item['quantity'],
                        'group_code' => $item['group_code'],
                        'Price' => $item['price'],
                        'Discount' => $item['discount']
                    );
                }
                $order_data[$key]->saleOrdeLineItems = $order_items;
            }
            return $order_data;  
        }catch(Exception $e){
            return json_encode(['success'=>0, "message"=>$e->getMessage()]);
        }      
    }
}
