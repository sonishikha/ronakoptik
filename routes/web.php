<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    //echo phpinfo();
    echo "New Page";
});

Route::view('refill','new');

Route::get("Refill_page",[ProductController::class,'getRefillData']);

Route::post("postinsert",[ProductController::class,'ajaxRequestPost']);

Route::get('/search',[ProductController::class,'search']);

Route::any('/category_filter',[ProductController::class,'category_filter']);
