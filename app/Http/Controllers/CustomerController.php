<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ApiValidation;

use App\Customer;
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
            // Validate request parameters and user
            $api_validation = new ApiValidation;
            $user = $api_validation->validateAndGetUser($request);
            
            //Get user region
            $region = $api_validation->getUserRegion($user->id);
            if(!$region){
                throw new Exception('User Region Not Found.');
            }

            //Get customer details
            $customers = Customer::where('Region__c', $region->region_code)
                                ->where('address_Type__c', 'B')
                                ->paginate(10, ['*'], 'page', $request->offSet);
            
            if($customers->count() == 0){
                throw new Exception('Customer Not Found.');
            }

            $customers->map(function($customer){
                //Get customer shipping addresses
                $customer->ship_to_party = (object)['records'=>[]];
                $shipping_customers = Customer::select('Address_Street__c','Address_Block__c','Address_Building__c','Address_StreetNo__c','Address_City__c','Address_ZipCode__c','Address_State__c','Address_Country_cc','GSTIN__c','GSTIN_TYPE__c')
                            ->where('BP_Code__c', $customer->BP_Code__c)
                            ->where('address_Type__c', 'S')->get();
                if($shipping_customers){
                    foreach($shipping_customers as $key=>$shipping_customer){
                        $customer->ship_to_party->records[$key] = $shipping_customer;
                    }
                }

                //Get customer PDC
                $customer->pdc = (object)['records'=>[]];
                $pdc_customers = DB::table('Vw_PDC_OnHold_Data')->where('Customer_Code__c', $customer->BP_Code__c)->get();
                if($pdc_customers){
                    foreach($pdc_customers as $key=>$pdc_customer){
                        $customer->pdc->records[$key] = $pdc_customer;
                    }
                }

                //Get Customer Ageing
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

}
