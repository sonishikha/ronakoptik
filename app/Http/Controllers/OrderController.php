<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Model\OrderItems;
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
}
