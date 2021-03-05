<!-- <//?php defined('SECURE') or die; ?> -->
<?php

// @if($totalSelectedMonths == 0) {{ "checked"}} @else if(in_array('APR',$monthGrp)){{"checked"}} @endif

$fullnameLoggedIn = "";
	$fUserTypeNo = !empty($fUser->type) ? $fUser->type : 0;
  /*look for this later*/
	// $userIdoggedin = $fUser->id;
	// $usAllerDetails = get_userdetails($userIdoggedin);
	// $fullnameLoggedIn = $usAllerDetails[0]['fullname'];

//echo $userIdoggedin;
?>
<?php
$customerGroup = array();
$monthGrp = array();
$display  = $customerName = $year = $toDate = $fromDate = $customerGroupStrInIf = $customerGroupStr = "";
if($_POST){
	//print_r($_POST);
//die();
	if(array_key_exists("year",$_POST)){
		$fromDate = $_POST['year'];
	} else {
		$fromDate = "";
	}
	if(array_key_exists("year",$_POST)){
		$toDate = $_POST['year'];
	} else {
		$toDate = "";


	}

	if(array_key_exists("year",$_POST)){
		$year = $_POST['year'];
	} else {
		$year = "";
	}


	if(array_key_exists("monthGrp",$_POST)){
		$monthGrp = $_POST['monthGrp'];

		$totalSelectedMonths = sizeof($monthGrp);

	} else {
		$monthGrp = array();
	}

	if(array_key_exists("customerGroup",$_POST)){
		$customerGroup = $_POST['customerGroup'];

		//echo $customerGroup;
		foreach($customerGroup as $s){
			$custGrp[] = "'".$s."'";
		}
		$totalSelected = sizeof($customerGroup);
		$customerGroupStr = implode (",", $custGrp);
		$customerGroupStrInIf = implode (", ", $customerGroup);
	} else {
		$customerGroupStrInIf = $customerGroupStr = "";
		$customerGroup = array();
	}

	if(array_key_exists("customerName",$_POST)){
		$customerName = $_POST['customerName'];
	} else {
		$customerName = "";
	}

	if(array_key_exists("display",$_POST)){
		$display = $_POST['display'];
	} else {
		$display = "";
	}

}
?>
<?php use \App\Http\Controllers\ProductController; ?>

<!DOCTYPE html>
	<html>
		<head>

			<meta charset="utf-8">
    		<meta http-equiv="X-UA-Compatible" content="IE=edge">
    		<meta name="viewport" content="width=device-width, initial-scale=1">
    		<link rel="icon" type="image/png" href="http://portal.ronakoptik.com/retail/templates/roptik/images/favicon.png">
    		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">

    		</script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">

			</script>
			<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">

			</script>

			<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-typeahead/2.10.6/jquery.typeahead.js">

			</script>

			<script src=”http://code.jquery.com/jquery-3.1.1.min.js”></script>

      <script src = "https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
      <link rel="stylesheet" href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}" />



			<link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,700,800" rel="stylesheet">

			<link href="https://fonts.googleapis.com/css?family=Tinos&display=swap" rel="stylesheet">



			<style>
				div, span, h1, h2, h3, h4, h5, h6, p{
					/*font-family: 'Montserrat', sans-serif;*/
				}
				body{
					/*padding:30px 10px;*/
					min-height: 100vh;
				}
.row{
	overflow: visible!important;
}
				.nomargin{
					margin:0!important;
				}
				.width100{
					width:100%;
				}

				tbody, th, table{
					border:1px solid #ccc;
				}

				.table{
					width: 100%;
    				margin: 20px 15px;
				}
				td{
					border:0!important;
					border-right: 1px solid #ccc!important;
				}
				th{
					border-bottom: 1px solid #ccc!important;
					    background-color: #ddd;
    				padding: 5px 3px;
				}
				.table2 td,
				.table2 th{
					text-align: center;
					font-size: 10px;
					max-width:100px;
				}
				.filterBox{
					margin: 10px 0;
				}
				.filterButton{
					width: 100%;
				    background-image: none!important;
				    background: #eee;
				    border: 1px solid #ddd;
				    padding: 4px;
				    color: #555;
				    transition: all 0.3s ease;
				        margin: 17px 0 ;
				        font-size: 11px;
				}
				.filterButton:hover{
				    background: #ccc;

				}
				.f-table-striped tbody tr:nth-child(even) td {
 			   background-color: #ddd;
}
				.falr{
 			   background-color: #ddd;

				}
				.no-list {
				    padding: 88px 0;
				    text-align: center;
				}
				.inputFields{
					width: 100%;
    				padding: 3px;
    				font-size: 11px;
				}
				.filterBox{
					    background-color: #F8F8F8;
				    border: 1px solid #ccc;
				    margin-bottom: 0em;
				    /*overflow: hidden;*/
				    padding: 10px 10px 0 10px;
				}
				.width95{
					width:95%;
					margin:0 auto;
				}
				.width100{
					width:100%;
				}
				.table2{
					min-width: 100%;
				}
				.dropdown-menu{
					width: 100%;
    				padding: 2px;
    				transform: translate3d(0px, 80px, 0px)!important;
    				max-height:65vh;
    				overflow-y:auto;
    				font-size: 13px!important;
    				margin-top:-30px;
				}
				.ui-corner-all{
					    display: inline-block;
   					 margin: 5px 0px 1px 15px;
				}
				.selectAll{
					    background: #1b3c79;
    color: #fff;
    padding: 5px;
				}
				.selectOption{
					width:100%;
					    width: 100%;
				    padding: 4px;
				    background: #fff;
				    font-size: 11px;
				        border: 1px solid #c5dbec;
    background-color: #dfeffc;
    margin-bottom:10px;
				}

				.dropdown-toggle::after{
					margin-top: 6px;
    				float: right;
				}
				.ui-multiselect-optgroup-label{
					color:#000;
					font-weight: 900;
				}

				.ui-multiselect-input{
					    margin-right: 10px;
				    width: 14px;
				    height: 14px;
				}
				p{
					font-size: 11px;
    margin-bottom: 3px;
				}
				.errorLine{
					color:red;
					font-size: 13px;

				}
				.infoLine{
					font-size: 13px;
				}

				.whiteText{
					color:#fff;
				}

				 .foot-cont {
				    background-color: #224091;
				    background: -moz-linear-gradient(top, #224091 0%, #1c3c75 60%);
				    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#224091), color-stop(60%,#1c3c75));
				    background: -webkit-linear-gradient(top, #224091 0%,#1c3c75 60%);
				    background: -o-linear-gradient(top, #224091 0%,#1c3c75 60%);
				    background: -ms-linear-gradient(top, #224091 0%,#1c3c75 60%);
				    background: linear-gradient(to bottom, #224091 0%,#1c3c75 60%);
				    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#224091', endColorstr='#1c3c75',GradientType=0 );
				    box-shadow: 0 0 0.5em #666;
				    color: #fff;

			    margin-top: 50px;
			    padding: 5px;
			    bottom: 0;
    /*position: absolute;*/
				}

				.head-cont {
				    background-color: #224091;
				    background: -moz-linear-gradient(top, #224091 0%, #1c3c75 60%);
				    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#224091), color-stop(60%,#1c3c75));
				    background: -webkit-linear-gradient(top, #224091 0%,#1c3c75 60%);
				    background: -o-linear-gradient(top, #224091 0%,#1c3c75 60%);
				    background: -ms-linear-gradient(top, #224091 0%,#1c3c75 60%);
				    background: linear-gradient(to bottom, #224091 0%,#1c3c75 60%);
				    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#224091', endColorstr='#1c3c75',GradientType=0 );
				    box-shadow: 0 0 0.5em #666;
				    color: #fff;

			    margin-bottom: 20px;
			    padding: 5px;
				}

				.compayName{
					font-family: 'Tinos', serif!important;

				}

				.foot-cont p{
					margin: 0
				}

				.powered-by {
				    opacity: .5;
				}

				.table2 tbody tr:nth-child(even) td {
 					   background-color: #ddd;
				}
        .form-control {
          padding:0px;
          border-radius: 0px;
          border:1px solid #fff;
        }
        .check_result{
          padding: 5px;
          margin: 10px;
          border-radius: 5px;
          font-size: 11px;
          background: #ddd;
          border: 1px solid #fff;
        }
        .fa {
          font-size: 13px;
          padding-left: 5px;
        }
        .errspan{
          float: right;
          position: absolute;
          margin-left: -3%;
          margin-top: 1%;
          color: red;
        }

			</style>
		</head>
		<body>
		<!-- <div class="width95"> -->
		<div class="head-cont">
						<div class="container">
								<div class="row">
										<div class="col-xs-6">
												<a href="/#/" id="logo">
														<i class="fa fa-globe"></i>
														<span class="visible-lg-inline whiteText compayName">Ronak Optik India Pvt. Ltd.</span>

												</a>
										</div>

								</div>
						</div>
				</div>
			<div class="container">
				<form action="category_filter" onsubmit="validateFilters(event)" method="get" enctype="multipart/form-data">
          @csrf
					<div class="filterBox">
            <div class="row">
              <div id="checkbox_result">
                <!-- selected brands -->
                @if($input_brands)
                  @for($i=0;$i<count($input_brands); $i++)
                    <input id="remove{{$input_brands[$i]}}" onclick="remove_filter(this.value)" type="text" class="check_result" value="{{$input_brands[$i]}}"><span id="remove1{{$input_brands[$i]}}" class="fa fa-times errspan" aria-hidden="true" readonly></span>
                  @endfor
                @endif
                <!-- selected collection -->
                @if($input_collection)
                  @for($i=0;$i<count($input_collection); $i++)
                    <input id="remove{{$input_collection[$i]}}" onclick="remove_filter(this.value)" type="text" class="check_result" value="{{$input_collection[$i]}}"><span id="remove1{{$input_collection[$i]}}" class="fa fa-times errspan" aria-hidden="true" readonly></span>
                  @endfor
                @endif
                <!-- selectred warehouses -->
                @if($selectedwarehouse)
                  @for($i=0;$i<count($selectedwarehouse); $i++)
                    <input id="remove{{$selectedwarehouse[$i]}}" onclick="remove_filter(this.value)" type="text" class="check_result" value="{{$selectedwarehouse[$i]}}"><span id="remove1{{$selectedwarehouse[$i]}}" class="fa fa-times errspan" aria-hidden="true" readonly></span>
                  @endfor
                @endif
              </div>
            </div>
						<div class="row">
							<div class="col-md-3 dropdown">
								<p><b>Brand</b></p>
                <p class="dropdown-toggle selectOption" type="button" id="menu2" data-toggle="dropdown">
                  select
                </p>
                <ul class="dropdown-menu" role="menu" aria-labelledby="menu2">
                  <li class=" selectAll">	<input type="checkbox" class="ui-multiselect-input" id="checkAll2">Select All</li>
                  <fieldset id="checkarray">
                    @foreach($brands as $b)
                    <?php $replaced_id = str_replace(' ','',$b->item_brand); ?>
  							      <li class=" ">
                        <label for="{{$b->item_brand}}" title="" class="ui-corner-all">
                          <input id="{{$replaced_id}}" onclick="show_result(this.value)" class="ui-multiselect-input brandMultiSelect" name="brands[]" value="{{$b->item_brand}}" title="{{$b->item_brand}}" type="checkbox"   @if(in_array($b->item_brand,$input_brands)){{'checked'}}@endif>
                          <span>{{$b->item_brand}}</span>
                        </label>
                      </li>
                    @endforeach
                  </fieldset>
  						  </ul>
  						</div>
							<div class="col-md-3 dropdown">
								<p><b>Collection</b></p>
								<p class="dropdown-toggle selectOption" type="button" id="menu1" data-toggle="dropdown">
                  select
                </p>
							<ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
								 <li class=" selectAll">	<input type="checkbox" class="ui-multiselect-input" id="checkAll">Select All</li>
                 @foreach($collection as $c)
                   <li class=" ">
                     <label for="{{$c->collection_name}}" title="" class="ui-corner-all">
                       <input id="{{$c->collection_name}}" onclick="show_result(this.value)" class="ui-multiselect-input collectionMultiSelect" name="collections[]" value="{{$c->collection_name}}" title="{{$c->collection_name}}" type="checkbox"  @if(in_array($c->collection_name,$input_collection)){{'checked'}}@endif>
                       <span>{{$c->collection_name}}</span>
                     </label>
                   </li>
                 @endforeach
						    </ul>
							</div>
              <div class="col-md-3 dropdown">
								<p><b>Warehouse</b></p>
                <p class="dropdown-toggle selectOption" type="button" id="menu3" data-toggle="dropdown">
                  select
                </p>
                <ul class="dropdown-menu" role="menu" aria-labelledby="menu3">
                  <li class=" selectAll">	<input type="checkbox" class="ui-multiselect-input" id="checkAll3">Select All</li>
                    @foreach($warehouse as $w)
  							      <li class=" ">
                        <label for="{{$w->whscode}}" title="" class="ui-corner-all">
                          <input id="{{$w->whscode}}" onclick="show_result(this.value)" class="ui-multiselect-input warehouseMultiSelect" name="warehouse[]" value="{{$w->whscode}}" title="{{$w->whscode}}" type="checkbox"  @if(in_array($w->whscode,$selectedwarehouse)){{'checked'}}@endif>
                          <span>{{$w->whscode}}</span>
                        </label>
                      </li>
                    @endforeach
  						  </ul>
  						</div>
							<div class="col-md-3">
								<p title="If you Search a Item name, Brand and collection selection would the ignored."><b>Item Name</b></p>
								<input class="inputFields typeahead" placeholder="Search Item by Name" name="customerName" onkeyup="custName(this.value)" type="text"  list="txtHint"  >
								<datalist  id="txtHint">

								</datalist>


							</div>

							<div class="col-md-3">

							</div>
						</div>
					</div>
          <div class="row">
              <div class="col-md-3"></div>
              <div class="col-md-3"></div>
              <div class="col-md-3"></div>
              <div class="col-md-3">
                  <button class="filterButton" type="submit">Filter</button>
              </div>
          </div>
				</form>


<div class="table-enclosure" style="width:100%;overflow-x:auto;">
  <table class="table2" id="report">
    <thead style="white-space:nowrap">
      <tr>
        <th>Brand</th>
        <th>Collection</th>
        <th style="width:30%;">Item Name</th>
        <!-- <th>Warehouse</th> -->
        <th>Stock in System</th>
        <!-- <th>Actuals</th> -->
        <th id="search_warehouse" style="display:none;">Warehouse</th>

        @if($selectedwarehouse)
          @for($i=0;$i<count($selectedwarehouse); $i++)
          <th>{{$selectedwarehouse[$i]}}</th>
					<th>{{$selectedwarehouse[$i]}} actuals</th>
          @endfor
        @endif

      </tr>
    </thead>
    <tbody>
      <!-- <//?php
      echo '<pre>';
      print_r($data);
      die();?> -->
      @if(sizeof($data)>0)
      @foreach($data as $d)
      <tr>
        <td>{{$d->item_brand}}</td>
        <td>{{$d->collection_name}}</td>
        <td>{{$d->item_name}}</td>
        <!-- <td>{{$d->whscode}}</td> -->
        <td>{{$d->onhand}}</td>

        @if($selectedwarehouse)
          @for($i=0;$i<count($selectedwarehouse); $i++)
            <?php $main_product_count = ProductController::main_product_count($selectedwarehouse[$i],$d->item_name);
            $tv = intval($main_product_count->truevalue);?>
              @if($tv > 0)
              <?php $tv = $tv ?>
              @else
              <?php $tv = 0; ?>
              @endif
            <td>{{$tv}}</td>
						<td><input type="text" name="actual_value" id="actual_value{{$d->itemcode}}" class="form-control" onblur="add_record(this.value,'{{$d->item_brand}}','{{$d->collection_name}}','{{$d->item_name}}','{{$d->onhand}}','{{$selectedwarehouse[$i]}}' )"></td>
          @endfor
        @endif
      </tr>
      @endforeach
      @else
      <tr><td colspan="6">No data Available</td></tr>
      @endif
    </tbody>
  </table>
</div>

        <br>
        <div class="row">

          <div class="col-md-12">

            <div class="pagination">
              {{$data->links()}}
            </div>
          </div>

        </div>
	</div>




<script>

var xport = {
  _fallbacktoCSV: true,
  toXLS: function(tableId, filename) {
    this._filename = (typeof filename == 'undefined') ? tableId : filename;

    //var ieVersion = this._getMsieVersion();
    //Fallback to CSV for IE & Edge
    if ((this._getMsieVersion() || this._isFirefox()) && this._fallbacktoCSV) {
      return this.toCSV(tableId);
    } else if (this._getMsieVersion() || this._isFirefox()) {
      alert("Not supported browser");
    }

    //Other Browser can download xls
    var htmltable = document.getElementById(tableId);
    var html = htmltable.outerHTML;

    this._downloadAnchor("data:application/vnd.ms-excel" + encodeURIComponent(html), 'xls');
  },
  toCSV: function(tableId, filename) {
    this._filename = (typeof filename === 'undefined') ? tableId : filename;
    // Generate our CSV string from out HTML Table
    var csv = this._tableToCSV(document.getElementById(tableId));
    // Create a CSV Blob
    var blob = new Blob([csv], { type: "text/csv" });

    // Determine which approach to take for the download
    if (navigator.msSaveOrOpenBlob) {
      // Works for Internet Explorer and Microsoft Edge
      navigator.msSaveOrOpenBlob(blob, this._filename + ".csv");
    } else {
      this._downloadAnchor(URL.createObjectURL(blob), 'csv');
    }
  },
  _getMsieVersion: function() {
    var ua = window.navigator.userAgent;

    var msie = ua.indexOf("MSIE ");
    if (msie > 0) {
      // IE 10 or older => return version number
      return parseInt(ua.substring(msie + 5, ua.indexOf(".", msie)), 10);
    }

    var trident = ua.indexOf("Trident/");
    if (trident > 0) {
      // IE 11 => return version number
      var rv = ua.indexOf("rv:");
      return parseInt(ua.substring(rv + 3, ua.indexOf(".", rv)), 10);
    }

    var edge = ua.indexOf("Edge/");
    if (edge > 0) {
      // Edge (IE 12+) => return version number
      return parseInt(ua.substring(edge + 5, ua.indexOf(".", edge)), 10);
    }

    // other browser
    return false;
  },
  _isFirefox: function(){
    if (navigator.userAgent.indexOf("Firefox") > 0) {
      return 1;
    }

    return 0;
  },
  _downloadAnchor: function(content, ext) {
      var anchor = document.createElement("a");
      anchor.style = "display:none !important";
      anchor.id = "downloadanchor";
      document.body.appendChild(anchor);

      // If the [download] attribute is supported, try to use it

      if ("download" in anchor) {
        anchor.download = this._filename + "." + ext;
      }
      anchor.href = content;
      anchor.click();
      anchor.remove();
  },
  _tableToCSV: function(table) {
    // We'll be co-opting `slice` to create arrays
    var slice = Array.prototype.slice;

    return slice
      .call(table.rows)
      .map(function(row) {
        return slice
          .call(row.cells)
          .map(function(cell) {
            return '"t"'.replace("t", cell.textContent);
          })
          .join(",");
      })
      .join("\r\n");
  }
};


</script>
<script>
  function show_result(value){
    var replaced_value = value.replace(/\s/g, "");
    var replaced_value_quoted = "'"+value.replace(/\s/g, "")+"'";
  //  alert(replaced_value)
    var checkbox = document.getElementById(replaced_value);
    if(checkbox.checked == true){
      $('#checkbox_result').append('<input id="remove'+replaced_value+'" onclick="remove_filter('+replaced_value_quoted+')" type="text" class="check_result" value="'+value+'"><span id="remove1'+replaced_value+'" class="fa fa-times errspan" aria-hidden="true"></span>');
    } else {
      $('#'+value).prop('checked',false);
      $('#remove'+replaced_value).hide();
      $('#remove1'+replaced_value).hide();
    }
  }


</script>
<script>
function remove_filter(value){
//  alert(value);
  $('#'+value).prop('checked',false);
  $('#remove'+value).hide();
  $('#remove1'+value).hide();
}
</script>
<script>
	function custName(val){
    // alert(val);
    var value = val;
    if(value.length > 2){
      $.ajax({
        type : 'get',
        url : '{{URL::to('search')}}',
        data:{search:value},
        success:function(data){
        $('tbody').html(data);
        $('#search_warehouse').show();
        }
      });
    } else {
      $('tbody').html('<tr>Enter Item name to search</tr>');
    }


  }

</script>

<script>

 $("#checkAll").click(function () {
     $('input.collectionMultiSelect:checkbox').not(this).prop('checked', this.checked);
 });

 $("#checkAll3").click(function () {
     $('input.warehouseMultiSelect:checkbox').not(this).prop('checked', this.checked);
 });

 $("#checkAll2").click(function () {
     $('input.brandMultiSelect:checkbox').not(this).prop('checked', this.checked);
 });

</script>

<script>

 $("#checkAllGrps").click(function () {
     $('input.grpMultiSelect:checkbox').not(this).prop('checked', this.checked);
 });

</script>
<script>
  function validateFilters(e) {

    var brandCount = $('input[name="brands[]"]:checked').length;
    var collectionCount = $('input[name="collections[]"]:checked').length;
    var warehouseCount = $('input[name="warehouse[]"]:checked').length;
    // alert(brandCount);
    if(brandCount < 1){
      e.preventDefault();
      alert('Please select atlest one Brand');
      return false;
    }
    if(collectionCount < 1){
      e.preventDefault();
      alert('Please select atlest one Collection');
      return false;
    }
    if(warehouseCount < 1){
      e.preventDefault();
      alert('Please select atlest one Warehouse');
      return false;
    }

  }
</script>
<script>
$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
  function add_record(value,brand,collection,item_name,stock_in_system,actual_warehouse){
		alert(actual_warehouse);
    var value = value;
    var brand = brand;
    var collection = collection;
    var item_name = item_name;
    var stock_in_system = stock_in_system;
		var actual_warehouse = actual_warehouse;
    var url = '{{ url('postinsert') }}';

    $.ajax({
       url:url,
       method:'POST',
       data:{
              brand:brand,
              collection:collection,
              item_name:item_name,
              stock_in_system:stock_in_system,
              value:value,
							actual_warehouse:actual_warehouse
            },
       success:function(response){
          if(response.success){
              alert(response.message) //Message come from controller
          }else{
              alert("Error")
          }
       },
       error:function(error){
          console.log(error)
       }
    });

  }
</script>


</body>
</html>
