<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ApiValidation;

use App\Product;
use Exception;


class ProductController extends Controller
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
            
            //Get user brands
            $brands = $api_validation->getUserBrand($user->id);
            
            if($brands->count() == 0){
                throw new Exception('User Brand Not Found.');
            }
            $brandcode = $brands->pluck('brandcode');
            
            $products = Product::whereIn('Item_Group_Code__c', $brandcode)
                                ->join('VW_Item_PriceList','Vw_ItemMaster.Item_Code__c','=','VW_Item_PriceList.ItemCode')
                                ->paginate(100, ['*'], 'page', $request->offSet);
            
            if($products->count() == 0){
                throw new Exception('Products Not Found.');
            }
            
            return $products;
        }catch(Exception $e){
            return json_encode(['success'=>false, "message"=>$e->getMessage()]);
        }
    }
}
