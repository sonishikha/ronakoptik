<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Customer;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Exception;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $validator = $this->validateRequest($request);
            if($validator->fails()){
                throw New Exception($validator->messages());
            }
           
            $user = User::where('email', '=', $request->userName)->firstOr(function () {
                throw new Exception('Username Not Found');
            });
            $region = User::find($user->id)->region;
            if(!$region){
                throw new Exception('User Region Not Found.');
            }
            $region_code = $region->region_code;
            
            $customers = Customer::where('Region__c', $region_code)
                                ->where('address_Type__c', 'B')
                                ->paginate(10, ['*'], 'page', $request->offSet);
            
            if($customers->total() == 0){
                throw new Exception('Customer Not Found.');
            }

            $customers->map(function($customer){
                $customer->ship_to_party = (object)['records'=>[]];
                $shipping_customers = Customer::select('Address_Street__c','Address_Block__c','Address_Building__c','Address_StreetNo__c','Address_City__c','Address_ZipCode__c','Address_State__c','Address_Country_cc','GSTIN__c','GSTIN_TYPE__c')
                            ->where('BP_Code__c', $customer->BP_Code__c)
                            ->where('address_Type__c', 'S')->get();
                if($shipping_customers){
                    foreach($shipping_customers as $key=>$shipping_customer){
                        $customer->ship_to_party->records[$key] = $shipping_customer;
                    }
                }

                $customer->pdc = (object)['records'=>[]];
                $pdc_customers = DB::table('Vw_PDC_OnHold_Data')->where('Customer_Code__c', $customer->BP_Code__c)->get();
                if($pdc_customers){
                    foreach($pdc_customers as $key=>$pdc_customer){
                        $customer->pdc->records[$key] = $pdc_customer;
                    }
                }

                $customer_ageing = DB::table('VW_CustomerAgeing')
                                    ->select('0 - 30', '31 - 60', '61 - 90', '91 - 120', '121 - 150', '151 - 180', '181 - 240', '241 - 300', '301 - 360', '361 +')
                                    ->where('Customer Code', $customer->BP_Code__c)->get();
                $customer['ageing'] = $customer_ageing;
                
                
                return $customer;
            });

            return $customers;
        }catch(Exception $e){
            return json_encode(['success'=>false, "message"=>$e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
       
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    
    public function validateRequest($request){
        $validator = Validator::make($request->all(), 
                    array(
                        "userName" => "required|email",
                        "offSet" => "required|int",
                        "sync" => ["required", Rule::in(['no','yes',"No","Yes","NO","YES"]),],
                    )
        );
        return $validator;
    }
}
