<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ApiValidation;

use App\Product;
use App\Warehouse;
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
            $brandcode = $this->validateAndGetUserBrand($request);
            $brands = (!empty($request->brand)) ? preg_split('#[,\s]+#', $request->brand) : [];
            $collection = (!empty($request->collection)) ? explode(', ', $request->collection) : [];
            $products = Product::whereIn('Item_Group_Code__c', $brandcode)
                                ->when(!empty($brands), function($query) use ($brands){
                                    return $query->whereIn('Brand__c', $brands);
                                })
                                ->when(!empty($collection), function($query) use ($collection){
                                    return $query->whereIn('Collection_Name__c', $collection);
                                })
                                ->leftjoin('VW_Item_PriceList','Vw_ItemMaster.Item_Code__c','=','VW_Item_PriceList.ItemCode')
                                ->paginate(200, ['*'], 'page', $request->offSet)
                                ->toArray();
            if(empty($products['data'])){
                throw new Exception('Products Not Found.');
            }
            return ['success'=>1] + $products;
        }catch(Exception $e){
            return json_encode(['success'=>0, "message"=>$e->getMessage()]);
        }
    }

    public function filter(Request $request){
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
                                ->leftjoin('VW_Item_PriceList','Vw_ItemMaster.Item_Code__c','=','VW_Item_PriceList.ItemCode')
                                ->get()->toArray();
            if(empty($products)){
                throw new Exception('Products Not Found.');
            }
            
            $data['data'] = array('collectionList'=>[], 'BRANDLIST'=>[], 'filGenderList'=>[], 'filmrpList'=>[], );
            foreach($products as $product){
                if(!empty($product['Collection__c']) && !in_array($product['Collection__c'], $data['data']['collectionList'])){
                    $data['data']['collectionList'][] = $product['Collection__c'];
                }
                if(!empty($product['Brand__c']) && !in_array($product['Brand__c'], $data['data']['BRANDLIST'])){
                    $data['data']['BRANDLIST'][] = $product['Brand__c'];
                }
                if(!empty($product['Category__c']) && !in_array(strtoupper($product['Category__c']), $data['data']['filGenderList'])){
                    $data['data']['filGenderList'][] = strtoupper($product['Category__c']);
                }
            } 
            $mrps = array_column($products, 'MRP');
            if(!empty($mrps)){
                $data['data']['filmrpList'][] = min($mrps);
                $data['data']['filmrpList'][] = max($mrps);
            }
            
            //Get user warehouse
            $warehouses = $api_validation->getUserWarehouse($user->id);
            if($warehouses->count() == 0){
                throw new Exception('User Warehouse Not Found.');
            }
            $warehouse_code = $warehouses->pluck('whouse_code');
            $warehouse_details = Warehouse::whereIn('Warehouse_Code__c', $warehouse_code)->get()->toArray();
            foreach($warehouse_details as $warehouse){
                $data['data']['WAREHOUSELIST'][] = $warehouse['Warehouse_Name__c'];
            }
            
            return json_encode(['success'=>1] + $data);
        }catch(Exception $e){
            return json_encode(['success'=>0, 'message'=>$e->getMessage()]);
        }
    }

    public function advanceFilter(Request $request){
        try{
            $brandcode = $this->validateAndGetUserBrand($request);

            $products = Product::whereIn('Item_Group_Code__c', $brandcode)
                                ->join('VW_Item_PriceList','Vw_ItemMaster.Item_Code__c','=','VW_Item_PriceList.ItemCode')
                                ->get()->toArray();
            if(empty($products)){
                throw new Exception('Products Not Found.');
            }
            
            $data['data'] = array('ws_price'=>[], 'tip_color'=>[], 'temple_material'=>[], 'temple_color'=>[],
                                'size_list'=>[], 'shape_list'=>[], 'front_color'=>[], 'frame_structure'=>[], 
                                'frame_material'=>[], 'lens_material'=>[]);
            foreach($products as $product){
                if(!empty($product['WHS Price']) && !in_array($product['WHS Price'], $data['data']['ws_price'])){
                    $data['data']['ws_price'][] = $product['WHS Price'];
                }
                if(!empty($product['Tips_Color__c']) && !in_array(strtoupper($product['Tips_Color__c']), $data['data']['tip_color'])){
                    $data['data']['tip_color'][] = strtoupper($product['Tips_Color__c']);
                }
                if(!empty($product['Temple_Material__c']) && !in_array(strtoupper($product['Temple_Material__c']), $data['data']['temple_material'])){
                    $data['data']['temple_material'][] = strtoupper($product['Temple_Material__c']);
                }
                if(!empty($product['Temple_Color__c']) && !in_array(strtoupper($product['Temple_Color__c']), $data['data']['temple_color'])){
                    $data['data']['temple_color'][] = strtoupper($product['Temple_Color__c']);
                }
                if(!empty($product['Size__c']) && !in_array(strtoupper($product['Size__c']), $data['data']['size_list'])){
                    $sizes = preg_split('#[\s]+#',$product['Size__c']);
                    $data['data']['filSizeList'][] = $sizes[0];
                    $data['data']['filSizeList1'][] = $sizes[1];
                    $data['data']['filSizeList2'][] = $sizes[2];
                }
                if(!empty($product['Shape__c']) && !in_array(strtoupper($product['Shape__c']), $data['data']['shape_list'])){
                    $data['data']['shape_list'][] = strtoupper($product['Shape__c']);
                }
                if(!empty($product['Front_Color__c']) && !in_array(strtoupper($product['Front_Color__c']), $data['data']['front_color'])){
                    $data['data']['front_color'][] = strtoupper($product['Front_Color__c']);
                }
                if(!empty($product['Frame_Structure__c']) && !in_array(strtoupper($product['Frame_Structure__c']), $data['data']['frame_structure'])){
                    $data['data']['frame_structure'][] = strtoupper($product['Frame_Structure__c']);
                }
                if(!empty($product['Frame_Material__c']) && !in_array(strtoupper($product['Frame_Material__c']), $data['data']['frame_material'])){
                    $data['data']['frame_material'][] = strtoupper($product['Frame_Material__c']);
                }
                if(!empty($product['Len_Material_c']) && !in_array(strtoupper($product['Len_Material_c']), $data['data']['lens_material'])){
                    $data['data']['lens_material'][] = strtoupper($product['Len_Material_c']);
                }
            } 
            return json_encode(['success'=>1] + $data);
        }catch(Exception $e){
            return json_encode(['success'=>0, 'message'=>$e->getMessage()]);
        }
    }

    protected function validateAndGetUserBrand($request){
        $api_validation = new ApiValidation;
        $user = $api_validation->validateAndGetUser($request);
        
        //Get user brands
        $brands = $api_validation->getUserBrand($user->id);
        
        if($brands->count() == 0){
            throw new Exception('User Brand Not Found.');
        }
        $brandcode = $brands->pluck('brandcode');
        return $brandcode;
    }
}
