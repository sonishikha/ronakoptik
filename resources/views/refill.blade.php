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

			<script src="http://code.jquery.com/jquery-3.1.1.min.js"></script>



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
				.nomargin{
					margin:0!important;
				}
				.width100{
					width:100%;
				}

				tbody, th, table{
					border:1px solid #ccc;
				}
				.row{
					overflow: visible!important;
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

			</style>
		</head>
		<body>
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

			<!-- <div class="width95"> -->
      <table class="table2" id="report">
      	<thead>
      		<tr>
      			<th>Brand</th>
      			<th>Collection</th>
      			<th>Item Name</th>
      			<th>Warehouse</th>
      			<th>Stock in System</th>
      			<th>Actuals</th>
      		</tr>
      	</thead>
      	<tbody>
          print_r($data);
          
          @foreach($data as $d)

      		<tr>
      			<td>{{$d->item_brand}}</td>
      			<td>{{$d->collection_name}}</td>
      			<td>{{$d->item_name}}</td>
      			<td>{{$d->whscode}}</td>
      			<td>{{$d->onhand}}</td>
      			<td><input type="text" name="actual_value" id="actual_value"</td>
      		</tr>
          @endforeach
      	</tbody>
      </table>
      <br>
      <div class="text-center">{{$data->links()}}</div>

    </body>
