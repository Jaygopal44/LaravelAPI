<?php

use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/insert',[ProductController::class,'insert']);
Route::get('/display_allUser',[ProductController::class,'display']);
Route::get('view/{id}',[ProductController::class,'view']);
Route::post('/update/{id}',[ProductController::class,'update']);
Route::delete('/delete/{id}',[ProductController::class,'del']);



