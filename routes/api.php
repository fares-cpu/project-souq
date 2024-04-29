<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(UserController::class)->group(function(){
    Route::get('/signup/params', 'store_params');
    Route::post('/signup', 'store');        //sign up
    Route::post('/login', 'login');         // login

});

Route::controller(ProductController::class)->group(function(){
    Route::post('/me/products/upload', 'store')->middleware('auth:sanctum');
    Route::get('/products/all', 'all');
    Route::get('/products/{product}', 'show');
    Route::post('/products/{product}/buy', 'buy')->middleware('auth:sanctum');
    Route::post('/products/{product}/update', 'update')->middleware('auth:sanctum');
    Route::post('/products/{product}/rate', 'rate')->middleware('auth:sanctum');
    Route::post('/products/{product}/report', 'report')->middleware('auth:sanctum');
    Route::post('/products/{product}/like', 'like')->middleware('auth:sanctum');
    Route::post('/products/{product}/comment', 'comment')->middleware('auth:sanctum');
    Route::post('/users/{user}/follow', 'followUser')->middleware('auth:sanctum');
    Route::post('/categories/{category}/follow', 'followCategory')->middleware('auth:sanctum');
    
});




