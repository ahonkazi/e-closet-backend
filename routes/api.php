<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::prefix('/public')->group(function (){
    Route::get('/category',[\App\Http\Controllers\CategoryController::class,'categories']); //get approved categories
    Route::get('/subcategory',[\App\Http\Controllers\SubCategoryController::class,'subCategories']); //get approved categories
});

Route::prefix('/vendor')->group(function (){
    
    Route::middleware(['auth:api','vendor'])->group(function(){
        Route::post('/logout',[\App\Http\Controllers\VendorAuthController::class,'logout']);
        Route::get('/notification',[\App\Http\Controllers\NotificationController::class,'notifications']);
        Route::delete('/notification/{id}',[\App\Http\Controllers\NotificationController::class,'delete']);
        Route::post('/read-notification/{id}',[\App\Http\Controllers\NotificationController::class,'readNotification']);
        Route::post('/unread-notification/{id}',[\App\Http\Controllers\NotificationController::class,'unReadNotification']);
    });
    Route::middleware(['auth:api','vendor','approvedVendor'])->group(function(){
        Route::get('/data',[\App\Http\Controllers\VendorAuthController::class,'data']);
        Route::post('/category',[\App\Http\Controllers\CategoryController::class,'add']);
        Route::post('/subcategory',[\App\Http\Controllers\SubCategoryController::class,'add']);
        Route::delete('/subcategory/{id}',[\App\Http\Controllers\SubCategoryController::class,'delete']);
        Route::delete('/category/{id}',[\App\Http\Controllers\CategoryController::class,'delete']);
        Route::get('/category',[\App\Http\Controllers\CategoryController::class,'allCategories']); //get all approved categories
        Route::get('/subcategory',[\App\Http\Controllers\SubCategoryController::class,'allSubCategories']); //get all approved categories
        Route::resource('/variation',\App\Http\Controllers\VariationController::class);
        Route::post('/product',[\App\Http\Controllers\ProductController::class,'addProduct']);
    });
 
        Route::middleware(['authLess'])->group(function(){
            Route::post('/send-register-otp',[\App\Http\Controllers\VendorAuthController::class,'sendOtp']);
            Route::post('/send-login-otp',[\App\Http\Controllers\VendorAuthController::class,'sendLoginOtp']);
            Route::post('/register',[\App\Http\Controllers\VendorAuthController::class,'register']);
            Route::post('/login',[\App\Http\Controllers\VendorAuthController::class,'login']);
        });
});

Route::prefix('/admin')->group(function (){
    Route::middleware(['auth:api','admin'])->group(function(){
        Route::post('/logout',[\App\Http\Controllers\AdminAuthcontroller::class,'logout']);
        Route::get('/notification',[\App\Http\Controllers\NotificationController::class,'notifications']);
        Route::delete('/notification/{id}',[\App\Http\Controllers\NotificationController::class,'delete']);
        Route::post('/read-notification/{id}',[\App\Http\Controllers\NotificationController::class,'readNotification']);
        Route::post('/unread-notification/{id}',[\App\Http\Controllers\NotificationController::class,'unReadNotification']);
        
    });
    Route::middleware(['auth:api','admin','approvedAdmin'])->group(function(){
        Route::get('/data',[\App\Http\Controllers\AdminAuthcontroller::class,'data']);
        Route::post('/approve-vendor/{vendor_id}',[\App\Http\Controllers\ApprovalController::class,'VendorApproval']);
        Route::post('/category',[\App\Http\Controllers\CategoryController::class,'add']);
        Route::post('/subcategory',[\App\Http\Controllers\SubCategoryController::class,'add']);
        Route::delete('/subcategory/{id}',[\App\Http\Controllers\SubCategoryController::class,'delete']);
        Route::delete('/category/{id}',[\App\Http\Controllers\CategoryController::class,'delete']);
        Route::post('/approve-category/{id}',[\App\Http\Controllers\CategoryController::class,'approve']);
        Route::post('/approve-subcategory/{id}',[\App\Http\Controllers\SubCategoryController::class,'approve']);
        Route::get('/category',[\App\Http\Controllers\CategoryController::class,'allCategories']); //get all approved categories
        Route::get('/subcategory',[\App\Http\Controllers\SubCategoryController::class,'allSubCategories']); //get all approved categories
        Route::post('/approve-product/{id}',[\App\Http\Controllers\ProductController::class,'approveProduct']);

    });
    Route::middleware(['authLess'])->group(function(){
        Route::post('/send-register-otp',[\App\Http\Controllers\AdminAuthcontroller::class,'sendOtp']);
        Route::post('/send-login-otp',[\App\Http\Controllers\AdminAuthcontroller::class,'sendLoginOtp']);
        Route::post('/register',[\App\Http\Controllers\AdminAuthcontroller::class,'register']);
        Route::post('/login',[\App\Http\Controllers\AdminAuthcontroller::class,'login']);
    });
});
