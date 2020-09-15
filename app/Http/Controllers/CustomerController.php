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
            $region = $region->pluck('region_code')->unique();
            //Get customer details
            $customers = Customer::whereIn('Region__c', $region)
                                ->where('address_Type__c', 'B')
                                ->paginate(200, ['*'], 'page', $request->offSet)->toArray();
            if(empty($customers['data'])){
                throw new Exception('Customer Not Found.');
            }
            $customer_ids = array_column($customers['data'],'BP_Code__c');
            
            //Customer Ageing Query
            $customer_ageing = DB::table('VW_CustomerAgeing')
                                ->select('Customer Code', '0 - 30 as x0_30__c ','31 - 60 as x31_60__c', '61 - 90 as x61_90__c', '91 - 120 as x91_120__c', '121 - 150 as x121_150__c', '151 - 180 as x151_180__c', '181 - 240 as x181_240__c', '241 - 300 as x241_300__c', '301 - 360 as x301_360__c', '361 + as x361__c') 
                                ->whereIn('Customer Code', $customer_ids)
                                ->get()->toArray();
            //Customer Shipping Address Query
            $shipping_customers = Customer::select('BP_Code__c','Address_Street__c','Address_Block__c','Address_Building__c','Address_StreetNo__c','Address_City__c','Address_ZipCode__c','Address_State__c','Address_Country_cc','GSTIN__c','GSTIN_TYPE__c')
                                ->whereIn('BP_Code__c', $customer_ids)
                                ->where('address_Type__c', 'S')
                                ->get()->toArray();
            //Customer PDC Query
            $pdc_customers = DB::table('Vw_PDC_OnHold_Data')
                                ->whereIn('Customer_Code__c', $customer_ids)
                                ->get()->toArray();

            foreach($customers['data'] as $key=>$customer){
                //Get Customer Ageing
                foreach($customer_ageing as $ageing){
                    $ageing_array = (array)$ageing;
                    foreach($ageing_array as $age_key=>$age){
                        if(is_numeric($age)){
                            $ageing_array[$age_key] = round($age);
                        }    
                    }
                    if($customer['BP_Code__c'] == $ageing_array['Customer Code']){
                        $customers['data'][$key] += $ageing_array;
                    }
                }
                //Get customer shipping addresses
                foreach($shipping_customers as $shipping_customer){
                    $shipping_array = (array)$shipping_customer;
                    if($customer['BP_Code__c'] == $shipping_array['BP_Code__c']){
                        $customers['data'][$key]['ship_to_party']['records'][] = $shipping_array;
                    }
                }
                //Get customer PDC
                foreach($pdc_customers as $pdc){
                    $pdc_array = (array)$pdc;
                    $pdc_array['Amount__c'] = round($pdc_array['Amount__c']);
                    if($customer['BP_Code__c'] == $pdc_array['Customer_Code__c']){
                        $customers['data'][$key]['pdc']['records'][] = $pdc_array;
                    }
                }
            }
            
            return ['success'=>1] + $customers;
        }catch(Exception $e){
            return json_encode(['success'=>0, 'message'=>$e->getMessage()]);
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
