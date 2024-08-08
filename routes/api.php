<?php

use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\GalleryController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [RegisterController::class, 'register']);
Route::put('/register/{id}', [RegisterController::class, 'edit']);
Route::get('/user', [RegisterController::class, 'show']);

Route::post('/login', [LoginController::class, 'login']);

//products
Route::apiResource('products', ProductController::class);

//contactus
Route::post('/contact-us', [ContactMessageController::class, 'store']);
Route::get('/contact-messages', [ContactMessageController::class, 'index']);
Route::delete('/contact-messages/{id}', [ContactMessageController::class, 'destroy']);

//gallery
Route::post('/gallery', [GalleryController::class, 'store']);
