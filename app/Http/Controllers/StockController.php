<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ApiValidation;

use App\Warehouse;
use App\User;
use App\Product;
use Exception;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $api_validation = new ApiValidation;
            $user = $api_validation->validateAndGetUser($request);
            //Get user warehouse
            $user_id = $user->id;
            
            $warehouses = $api_validation->getUserWarehouse($user->id);
            if($warehouses->count() == 0){
                throw new Exception('User Warehouse Not Found.');
            }
            $warehouse_code = $warehouses->pluck('whouse_code');
            $brand_codes = $api_validation->getUserBrand($user_id)->pluck('brandcode');

            $stock = Warehouse::select('Warehouse_Name__c','ItemCode as Item_Code__c', 'Ordered as Ordered_Quantity__c', 'OnHand as Stock__c')
                                ->whereIn('Warehouse_Code__c', $warehouse_code)
                                ->leftjoin('Vw_WarehouseStockDetails as Stock','Vw_WarehouseMaster.Warehouse_Code__c','=','Stock.WhsCode')
                                ->whereIn('ItemCode', function($query) use ($brand_codes){
                                    $query->select('Item_Code__c')->from('Vw_ItemMaster')->whereIn('Item_Group_Code__c', $brand_codes);
                                })
                                ->paginate(100, ['*'], 'page', $request->offSet)->toArray();
            
            if(empty($stock['data'])){
                throw New Exception('Stock Details Not Found.');
            }
            return json_encode(['success'=>1] + $stock);
        }catch(Exception $e){
            return json_encode(['success'=>0, "message"=>$e->getMessage()]);
        }
    }
}
