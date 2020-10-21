<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('register', 'JwtAuthController@register');
Route::post('login', 'JwtAuthController@authenticate');
Route::get('mysqluser', 'JwtAuthController@getuser');  

Route::group(['middleware' => ['jwt.verify','api.log']], function() {
    Route::get('user', 'JwtAuthController@getAuthenticatedUser');
    Route::post('customers', 'CustomerController@index');
    Route::post('products', 'ProductController@index');
    Route::post('product', 'ProductController@getProductDetails');
    Route::post('filter', 'ProductController@filter');
    Route::post('advance_filter', 'ProductController@advanceFilter');
    Route::post('stocks', 'StockController@index');
    Route::post('save_order', 'OrderController@store');
    Route::post('invoices', 'OrderController@getInvoiceList');
});
