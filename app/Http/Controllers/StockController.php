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
            $warehouses = $api_validation->getUserWarehouse($user->id);
            if($warehouses->count() == 0){
                throw new Exception('User Warehouse Not Found.');
            }
            $warehouse_code = $warehouses->pluck('whouse_code');
            $brands = preg_split('#[,\s]+#', $request->brand);

            $stock = Warehouse::select('Warehouse_Name__c as Warehouse_Name1__c','ItemCode as Item_Code__c', 'Ordered as Ordered_Quantity__c', 'OnHand as Stock__c')
                                ->whereIn('Warehouse_Code__c', $warehouse_code)
                                ->leftjoin('Vw_WarehouseStockDetails as Stock','Vw_WarehouseMaster.Warehouse_Code__c','=','Stock.WhsCode')
                                ->whereIn('ItemCode', function($query) use ($brands){
                                    $query->select('Item_Code__c')->from('Vw_ItemMaster')->whereIn('Brand__c', $brands);
                                })
                                ->paginate(100, ['*'], 'page', $request->offSet)->toArray();
            
            if(empty($stock['data'])){
                throw New Exception('Stock Details Not Found.');
            }
            foreach($stock['data'] as $key=>$value){
                $stock['data'][$key]['Ordered_Quantity__c'] = round($value['Ordered_Quantity__c']);
                $stock['data'][$key]['Stock__c'] = round($value['Stock__c']);
            }
            return json_encode(['success'=>1] + $stock);
        }catch(Exception $e){
            return json_encode(['success'=>0, "message"=>$e->getMessage()]);
        }
    }
}
