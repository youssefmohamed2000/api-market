<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BuyerController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SellerController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;


// AUTH
Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('logout', 'logout');
    Route::post('forgot-password', 'forgotPassword');
    Route::post('reset-password', 'resetPassword');
    Route::post('email/verification-notification', 'sendEmailVerification');
    Route::get('/email/verify/{id}/{hash}', 'verify')->name('verification.verify');
});


Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {
    // BUYERS
    Route::controller(BuyerController::class)->group(function () {
        Route::get('buyer/{id}/transactions', 'transactions');
        Route::get('buyer/{id}/products', 'products');
        Route::get('buyer/{id}/sellers', 'sellers');
        Route::get('buyer/{id}/categories', 'categories');
    });
    Route::apiResource('buyers', BuyerController::class)->only(['index', 'show']);

    // CATEGORIES
    Route::controller(CategoryController::class)->group(function () {
        Route::get('category/{id}/products', 'products');
        Route::get('category/{id}/sellers', 'sellers');
        Route::get('category/{id}/transactions', 'transactions');
        Route::get('category/{id}/buyers', 'buyers');
    });
    Route::apiResource('categories', CategoryController::class);

    // PRODUCTS
    Route::controller(ProductController::class)->group(function () {
        Route::get('product/{id}/category', 'category');
        Route::get('product/{id}/seller', 'seller');
        Route::get('product/{id}/transactions', 'transactions');
        Route::get('product/{id}/buyers', 'buyers');
    });
    Route::apiResource('products', ProductController::class);

    // SELLERS
    Route::controller(SellerController::class)->group(function () {
        Route::get('seller/{id}/products', 'products');
        Route::get('seller/{id}/categories', 'categories');
        Route::get('seller/{id}/transactions', 'transactions');
        Route::get('seller/{id}/buyers', 'buyers');
    });
    Route::apiResource('sellers', SellerController::class)->only(['index', 'show']);


    // TRANSACTIONS
    Route::controller(TransactionController::class)->group(function () {
        Route::get('transactions/{id}/category', 'category');
        Route::get('transactions/{id}/seller', 'seller');
        Route::post('transactions/store/{id}', 'store');
    });
    Route::apiResource('transactions', TransactionController::class)->only(['index', 'show']);

    // USERS
    Route::apiResource('users', UserController::class)->except('store');
});

