<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ApiValidation;
use App\Models\WarehouseDetails;
use App\Models\RoStockReconcile;

use App\Product;
use App\Warehouse;
use Exception;
use Illuminate\Support\Facades\Input;


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
            $user = $api_validation->validateAndGetUser($request, true);
            $brandcode = $this->getUserBrands($user->id);

            $brands = (!empty($request->brand)) ? preg_split('#[,\s]+#', $request->brand) : [];
            $collection = (!empty($request->collection)) ? preg_split('#[,\s]+#', $request->collection) : [];
            $products = Product::whereIn('Item_Group_Code__c', $brandcode)
                                ->when(!empty($brands), function($query) use ($brands){
                                    return $query->whereIn('Brand__c', $brands);
                                })
                                ->when(!empty($collection), function($query) use ($collection){
                                    return $query->whereIn('Collection__c', $collection);
                                })
                                ->leftjoin('VW_Item_PriceList','Vw_ItemMaster.Item_Code__c','=','VW_Item_PriceList.ItemCode')
                                ->orderBy('Collection__c','DESC')
                                ->paginate(200, ['*'], 'page', $request->offSet)
                                ->toArray();
            if(empty($products['data'])){
                throw new Exception('Products Not Found.');
            }
            $product_ids = array_column($products['data'],'Item_Code__c');
            $product_images = DB::table('Vw_ItemMaster_Image')
                            ->select('ItemCode','File Name')
                            ->whereIn('ItemCode', $product_ids)
                            ->get()->toArray();

            foreach($products['data'] as $key=>$product){
                $products['data'][$key]['MRP__c'] = round($product['MRP']);
                unset($products['data'][$key]['MRP']);
                $products['data'][$key]['WS_Price__c'] = round($product['WHS Price']);
                unset($products['data'][$key]['WHS Price']);

                $products['data'][$key]['product_images__c'] = '';
                foreach($product_images as $images){
                    $images = (array)$images;
                    if($product['Item_Code__c'] == $images['ItemCode']){
                        $products['data'][$key]['product_images__c'] .= $images['File Name'].', ';
                    }
                }
                $products['data'][$key]['product_images__c'] = trim($products['data'][$key]['product_images__c'],', ');
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
            $brandcode = $this->getUserBrands($user->id);

            $data['data'] = array('WAREHOUSELIST'=>[], 'WAREHOUSEBRANDLIST'=>[], 'BRANDLIST'=>[], 'collectionList'=>[], 'filGenderList'=>[], 'filmrpList'=>[]);
            //Get user warehouse
            $warehouses = $api_validation->getUserWarehouse($user->id);
            $warehouse_item_list = array();
            if($warehouses->count() != 0){
                $warehouse_code = $warehouses->pluck('whouse_code');
                $warehouse_details = Warehouse::select('Warehouse_Name__c','ItemCode')
                                                ->leftjoin('Vw_WarehouseStockDetails as Stock','Vw_WarehouseMaster.Warehouse_Code__c','=','Stock.WhsCode')
                                                ->whereIn('Warehouse_Code__c', $warehouse_code)
                                                ->get()->toArray();
                foreach($warehouse_details as $warehouse){
                    if(!in_array($warehouse['Warehouse_Name__c'], $data['data']['WAREHOUSELIST'])){
                        $data['data']['WAREHOUSELIST'][] = $warehouse['Warehouse_Name__c'];
                    }
                    if(!empty($warehouse['ItemCode'] && !in_array($warehouse['ItemCode'], $warehouse_item_list))){
                        $warehouse_item_list[] = $warehouse['ItemCode'];
                    }
                }
            }

            $products = Product::select('Collection__c', 'Brand__c', 'Category__c', 'Item_Code__c')
                                ->whereIn('Item_Group_Code__c', $brandcode)
                                ->get()->toArray();

            if(!empty($products)){
                foreach($products as $product){
                    if($product['Brand__c'] != null && !in_array($product['Brand__c'], $data['data']['WAREHOUSEBRANDLIST'])){
                        if(in_array($product['Item_Code__c'], $warehouse_item_list) ){
                            $data['data']['WAREHOUSEBRANDLIST'][] = $product['Brand__c'];
                        }
                    }
                    if(!empty($product['Collection__c']) && !in_array($product['Collection__c'], $data['data']['collectionList']) && $product['Collection__c'] != null){
                        $data['data']['collectionList'][] = $product['Collection__c'];
                    }
                    if(!empty($product['Brand__c']) && !in_array($product['Brand__c'], $data['data']['BRANDLIST'])  && $product['Brand__c'] != null){
                        $data['data']['BRANDLIST'][] = $product['Brand__c'];
                    }
                    if(!empty($product['Category__c']) && !in_array(strtoupper($product['Category__c']), $data['data']['filGenderList'])){
                        $data['data']['filGenderList'][] = strtoupper($product['Category__c']);
                    }
                }
            }
            return json_encode(['success'=>1] + $data);
        }catch(Exception $e){
            return json_encode(['success'=>0, 'message'=>$e->getMessage()]);
        }
    }

    public function advanceFilter(Request $request){
        try{
            $api_validation = new ApiValidation;
            $user = $api_validation->validateAndGetUser($request);
            $brandcode = $this->getUserBrands($user->id);

            $products = Product::select('MRP','WHS Price','Tips_Color__c','Temple_Material__c','Temple_Color__c','Size__c','Shape__c','Front_Color__c','Logo_Color__c','Frame_Structure__c','Frame_Material__c','Len_Material_c')
                                ->join('VW_Item_PriceList','Vw_ItemMaster.Item_Code__c','=','VW_Item_PriceList.ItemCode')
                                ->whereIn('Vw_ItemMaster.Item_Group_Code__c', $brandcode)
                                ->get()->toArray();

            if(empty($products)){
                throw new Exception('Products Not Found.');
            }

            $data['data'] = array('ws_price'=>[], 'tip_color'=>[], 'temple_material'=>[], 'temple_color'=>[],
                                'filSizeList'=>[], 'filSizeList1'=>[], 'filSizeList2'=>[], 'shape_list'=>[], 'front_color'=>[], 'frame_structure'=>[],
                                'frame_material'=>[], 'lens_material'=>[], 'logo_color'=>[]);
            foreach($products as $product){
                if((!empty($product['WHS Price']) || $product['WHS Price']!= null) && !in_array($product['WHS Price'], $data['data']['ws_price'])){
                    $data['data']['ws_price'][] = $product['WHS Price'];
                }
                if(!empty($product['Tips_Color__c'] || $product['Tips_Color__c'] != null) && !in_array(strtoupper($product['Tips_Color__c']), $data['data']['tip_color'])){
                    $data['data']['tip_color'][] = strtoupper($product['Tips_Color__c']);
                }
                if(!empty($product['Temple_Material__c'] || $product['Temple_Material__c'] != null) && !in_array(strtoupper($product['Temple_Material__c']), $data['data']['temple_material'])){
                    $data['data']['temple_material'][] = strtoupper($product['Temple_Material__c']);
                }
                if(!empty($product['Temple_Color__c'] || $product['Temple_Color__c'] != null) && !in_array(strtoupper($product['Temple_Color__c']), $data['data']['temple_color'])){
                    $data['data']['temple_color'][] = strtoupper($product['Temple_Color__c']);
                }
                if(!empty($product['Size__c'] || $product['Size__c']!= null)){
                    $sizes = preg_split('#[\s]+#',$product['Size__c']);
                    if(count($sizes) == 3){
                        if(!in_array($sizes[0], $data['data']['filSizeList'])){
                            $data['data']['filSizeList'][] = $sizes[0];
                        }
                        if(!in_array($sizes[1], $data['data']['filSizeList1'])){
                            $data['data']['filSizeList1'][] = $sizes[1];
                        }
                        if(!in_array($sizes[2], $data['data']['filSizeList2'])){
                            $data['data']['filSizeList2'][] = $sizes[2];
                        }
                    }
                }
                if(!empty($product['Shape__c'] || $product['Shape__c']!=null) && !in_array(strtoupper($product['Shape__c']), $data['data']['shape_list'])){
                    $data['data']['shape_list'][] = strtoupper($product['Shape__c']);
                }
                if(!empty($product['Front_Color__c'] || $product['Front_Color__c']!=null) && !in_array(strtoupper($product['Front_Color__c']), $data['data']['front_color'])){
                    $data['data']['front_color'][] = strtoupper($product['Front_Color__c']);
                }
                if(!empty($product['Frame_Structure__c'] || $product['Frame_Structure__c']!=null) && !in_array(strtoupper($product['Frame_Structure__c']), $data['data']['frame_structure'])){
                    $data['data']['frame_structure'][] = strtoupper($product['Frame_Structure__c']);
                }
                if(!empty($product['Frame_Material__c'] || $product['Frame_Material__c']!=null) && !in_array(strtoupper($product['Frame_Material__c']), $data['data']['frame_material'])){
                    $data['data']['frame_material'][] = strtoupper($product['Frame_Material__c']);
                }
                if(!empty($product['Len_Material_c'] || $product['Len_Material_c']!=null) && !in_array(strtoupper($product['Len_Material_c']), $data['data']['lens_material'])){
                    $data['data']['lens_material'][] = strtoupper($product['Len_Material_c']);
                }
                if(!empty($product['MRP'] || $product['MRP']!=null)){
                    $mrps[] = $product['MRP'];
                }
                if(!empty($product['Logo_Color__c'] || $product['Logo_Color__c']!=null) && !in_array(strtoupper($product['Logo_Color__c']), $data['data']['logo_color'])){
                    $data['data']['logo_color'][] = strtoupper($product['Logo_Color__c']);
                }
            }

            if(!empty($mrps)){
                $data['data']['filmrpList'][] = "".round(min($mrps))."";
                $data['data']['filmrpList'][] = "".round(max($mrps))."";
            }
            return json_encode(['success'=>1] + $data);
        }catch(Exception $e){
            return json_encode(['success'=>0, 'message'=>$e->getMessage()]);
        }
    }

    protected function getUserBrands($user_id){
        $api_validation = new ApiValidation;
        //Get user brands
        $brands = $api_validation->getUserBrand($user_id);

        if($brands->count() == 0){
            throw new Exception('User Brand Not Found.');
        }
        $brandcode = $brands->pluck('brandcode');
        return $brandcode;
    }

    public function getProductDetails(Request $request){
        try{
            if(empty($request->product_id)){
                throw New Exception('Please Provide Product Id.');
            }
            $product = Product::where('Item_Code__c', $request->product_id)
                            ->leftjoin('VW_Item_PriceList','Vw_ItemMaster.Item_Code__c','=','VW_Item_PriceList.ItemCode')
                            ->first()->toArray();
            if(empty($product)){
                throw New Exception('Product Not Found.');
            }
            $product['MRP__c'] = round($product['MRP']);
            unset($product['MRP']);
            $product['WS_Price__c'] = round($product['WHS Price']);
            unset($product['WHS Price']);

            $product_images = DB::table('Vw_ItemMaster_Image')
                            ->select('File Name')
                            ->where('ItemCode', $request->product_id)
                            ->get()->toArray();
            $product['product_images__c'] = (!empty($product_images)) ? implode(', ', array_column($product_images, 'File Name')) : '';
            return json_encode(['success'=>1] + ["data"=>$product]);
        }catch(Exception $e){
            return json_encode(['success'=>0, 'message'=>$e->getMessage()]);
        }
    }

    public function getRefillData(){
      $input_brands = array();
      $data1 = array();
      $input_collection = array();
      // $whnames  = array('main','Deepak' );
      // $data_final = DB::table('Vw_ItemMaster')->Join('Vw_WarehouseStockDetails', 'Item_Code__c', '=', 'ItemCode')
      //       ->select(['Item_Code__c as itemcode', 'Item_Name__c as item_name','Brand__c as item_brand','Product__c as product','Collection_Name__c as collection_name','WhsCode as whscode','OnHand as onhand'])
      //       ->whereHas('WhsCode',function($query) use ($whnames){
      //
      //       } )->get();
      //       print_r($data_final);
      //       die();
      $data = DB::table('Vw_ItemMaster')->Join('Vw_WarehouseStockDetails', 'Item_Code__c', '=', 'ItemCode')
            ->select([ DB::raw('MAX(Item_Code__c) AS itemcode'),'Item_Name__c as item_name',DB::raw('MAX(Brand__c) AS item_brand'),DB::raw('MAX(Collection_Name__c) AS collection_name'),DB::raw('MAX(WhsCode) AS whscode'),DB::raw('SUM(OnHand) AS onhand'),DB::raw('MAX(OnHand) AS maxi')])
            ->where('Item_Name__c', '=', '12345')
            // ->orderBy('users.updated_at', 'desc')

            ->groupBy('Item_Name__c')
            // ->groupBy('Collection_Name__c')
            // ->groupBy('Brand__c')
            // ->groupBy('WhsCode')
            ->paginate(15);
            // ->get();
            // echo '<pre>';
            //  print_r($data);
            //  die();
            $brands= DB::table('Vw_ItemMaster')->Join('Vw_WarehouseStockDetails', 'Item_Code__c', '=', 'ItemCode')
                  ->select(['Brand__c as item_brand'])
                  ->groupBy('Brand__c')
                  ->get();
            $collection= DB::table('Vw_ItemMaster')->Join('Vw_WarehouseStockDetails', 'Item_Code__c', '=', 'ItemCode')
                  ->select(['Collection_Name__c as collection_name'])
                  ->groupBy('Collection_Name__c')
                  ->get();
            $warehouse= DB::table('Vw_ItemMaster')->Join('Vw_WarehouseStockDetails', 'Item_Code__c', '=', 'ItemCode')
                  ->select(['WhsCode as whscode'])
                  ->groupBy('WhsCode')
                  ->get();
            $selectedwarehouse = array();
            //       echo '<pre>';
            // print_r($data);
            // die();

      return view('new',['data'=>$data,
                        'brands'=>$brands,
                        'collection'=>$collection,
                        'warehouse'=>$warehouse,
                        'input_brands'=>$input_brands,
                        'input_collection'=>$input_collection,
                        'selectedwarehouse'=>$selectedwarehouse
                      ]);
    }

    public function category_filter(Request $request)
    {

      $selectedbrands = $input_brands = implode(",",$_GET['brands']);
      $selectedwarehouse =  $input_warehouse = implode(",",$_GET['warehouse']);
      $selectedcollection = $input_collection = implode(",",$_GET['collections']);


      $data1 = DB::table('Vw_ItemMaster')->Join('Vw_WarehouseStockDetails', 'Item_Code__c', '=', 'ItemCode')
            ->select([ DB::raw('MAX(Item_Code__c) AS itemcode'),'Item_Name__c as item_name',DB::raw('MAX(Brand__c) AS item_brand'),DB::raw('MAX(Collection_Name__c) AS collection_name'),DB::raw('MAX(WhsCode) AS whscode'),DB::raw('SUM(OnHand) AS onhand'),DB::raw('MAX(OnHand) AS maxi')])
            ->whereIn('Brand__c',explode(",",$input_brands))
            ->whereIn('Collection_Name__c',explode(",",$input_collection))
            ->whereIn('WhsCode',explode(",",$input_warehouse))
            ->groupBy('Item_Name__c')
            ->paginate(10)->withQueryString();



      $brands= DB::table('Vw_ItemMaster')->Join('Vw_WarehouseStockDetails', 'Item_Code__c', '=', 'ItemCode')
            ->select(['Brand__c as item_brand'])
            ->groupBy('Brand__c')
            ->get();
      $collection= DB::table('Vw_ItemMaster')->Join('Vw_WarehouseStockDetails', 'Item_Code__c', '=', 'ItemCode')
            ->select(['Collection_Name__c as collection_name'])
            ->groupBy('Collection_Name__c')
            ->get();
      $warehouse= DB::table('Vw_ItemMaster')->Join('Vw_WarehouseStockDetails', 'Item_Code__c', '=', 'ItemCode')
            ->select(['WhsCode as whscode'])
            ->groupBy('WhsCode')
            ->get();
            $input_brands = explode(",",$input_brands);
            $input_collection = explode(",",$input_collection);
            $selectedwarehouse = explode(",",$selectedwarehouse);
      return view('new',
        ['data'=>$data1,
        'brands'=>$brands,
        'collection'=>$collection,
        'warehouse'=>$warehouse,
        'input_brands'=>$input_brands,
        'input_collection'=>$input_collection,
        'selectedwarehouse'=>$selectedwarehouse
        ]
      );

    }

    public function ajaxRequestPost(Request $request)
    {
      $date = date('Y-m-d H:i:s');
      $count = RoStockReconcile::where('item_name','=',$request->input('item_name'))->count();
      if($count < 1){
        $RoStockReconcile = new RoStockReconcile();
        $RoStockReconcile->brand = $request->input('brand');
        $RoStockReconcile->collection = $request->input('collection');
        $RoStockReconcile->item_name = $request->input('item_name');
        $RoStockReconcile->stock_in_sap = $request->input('stock_in_system');
        $RoStockReconcile->actual_stock = $request->input('value');
        $RoStockReconcile->actual_warehouse = $request->input('actual_warehouse');
        $RoStockReconcile->created_by = '1';
        $RoStockReconcile->updated_at = $date;
        $RoStockReconcile->updated_by = '1';

        $success = $RoStockReconcile->save();
        return response()->json(
            [
                'success' => true,
                'message' => 'Data inserted successfully'
            ]
        );
      } else {
        RoStockReconcile::where('item_name','=',$request->input('item_name'))
        // RoStockReconcile::where('actual_warehouse','=',$request->input('actual_warehouse'))
        ->update([
          'actual_stock' => $request->input('value'),
          'updated_at' => $date
        ]);
        return response()->json(
            [
                'success' => true,
                'message' => 'Data updated successfully'
            ]
        );
      }
    }

public function search(Request $request)
{
  if($request->ajax())
  {
  $output="";
  $products = DB::table('Vw_ItemMaster')->Join('Vw_WarehouseStockDetails', 'Item_Code__c', '=', 'ItemCode')
        ->select(['Item_Code__c as itemcode', 'Item_Name__c as item_name','Brand__c as item_brand','Product__c as product','Collection_Name__c as collection_name','WhsCode as whscode','OnHand as onhand'])
        ->where('Item_Name__c','LIKE','%'.$request->search."%")->paginate(50);


    if($products){
      foreach ($products as $key => $product) {
        $p_brand ="'".$product->item_brand."'";
        $p_c_name ="'".$product->collection_name."'";
        $p_i_name ="'".$product->item_name."'";
        $p_onhand ="'".$product->onhand."'";

        $output.='<tr>'.
        '<td>'.$product->item_brand.'</td>'.
        '<td>'.$product->collection_name.'</td>'.
        '<td>'.$product->item_name.'</td>'.
        '<td>'.$product->onhand.'</td>'.
        '<td><input type="text" name="actual_value" id="actual_value'.$product->itemcode.'" class="form-control" onblur="add_record(this.value,'.$p_brand.','.$p_c_name.','.$p_i_name.','.$p_onhand.' )"></td>'.
        '<td>'.$product->whscode.'</td>'.
        '</tr>';

        $output.='<br>'.
        '<div class="row">
          <div class="col-md-12">
          <div class="pagination"></div>
          </div>
        </div>';
      }
    return Response($output);
   }
  }
}

public static function main_product_count($whscode,$item_name){
  $data = DB::table('Vw_ItemMaster')->Join('Vw_WarehouseStockDetails', 'Item_Code__c', '=', 'ItemCode')
        ->select([DB::raw('SUM(OnHand) AS truevalue')],'WhsCode as actual_whscode')
        ->where('WhsCode',$whscode)
        ->where('Item_Name__c',$item_name)
        ->first();
        // print_r($data);
        // die();

  return $data;


}


}
