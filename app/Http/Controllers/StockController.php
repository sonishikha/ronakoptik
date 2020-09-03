<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ApiValidation;

use App\Stock;
use App\User;
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
            
            $stock = Stock::whereIn('Warehouse_Code__c', $warehouse_code)
                                ->join('Vw_WarehouseStockDetails','Vw_WarehouseMaster.Warehouse_Code__c','=','Vw_WarehouseStockDetails.WhsCode')
                                ->paginate(10, ['*'], 'page', $request->offSet);
            
            if($stock->count() == 0){
                throw New Exception('Stock Details Not Found.');
            }
            return $stock;
        }catch(Exception $e){
            return json_encode(['success'=>false, "message"=>$e->getMessage()]);
        }
    }
}
