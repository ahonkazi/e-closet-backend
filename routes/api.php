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
    Route::get('/product',[\App\Http\Controllers\ProductController::class,'getAllProducts']);
    Route::get('/search',[\App\Http\Controllers\ProductController::class,'search']);
});

//VENDOR
Route::prefix('/vendor')->group(function (){
    Route::middleware(['auth:api','vendor'])->group(function(){
        Route::post('/logout',[\App\Http\Controllers\VendorAuthController::class,'logout']);
        Route::get('/notification',[\App\Http\Controllers\NotificationController::class,'notifications']);
        Route::delete('/notification/{id}',[\App\Http\Controllers\NotificationController::class,'delete']);
        Route::post('/read-notification/{id}',[\App\Http\Controllers\NotificationController::class,'readNotification']);
        Route::post('/unread-notification/{id}',[\App\Http\Controllers\NotificationController::class,'unReadNotification']);
        Route::post('/change-email-otp',[\App\Http\Controllers\SecurityController::class,'changeEmailOtp']);
        Route::post('/change-password',[\App\Http\Controllers\SecurityController::class,'changePassword']);
        Route::post('/change-email',[\App\Http\Controllers\SecurityController::class,'changeUserEmail']);

    });
    Route::middleware(['auth:api','vendor','approvedVendor'])->group(function(){
//        CATEGORIES AND SUB CATEGORIES START
        Route::post('/category',[\App\Http\Controllers\CategoryController::class,'add']);
        Route::post('/subcategory',[\App\Http\Controllers\SubCategoryController::class,'add']);
        Route::get('/subsubcategory',[\App\Http\Controllers\SubSubCategoryController::class,'index']); //get all approved categories
        Route::post('/subsubcategory',[\App\Http\Controllers\SubSubCategoryController::class,'add']); //get all approved categories
        Route::delete('/subsubcategory/{id}',[\App\Http\Controllers\SubSubCategoryController::class,'delete']);
        Route::delete('/subcategory/{id}',[\App\Http\Controllers\SubCategoryController::class,'delete']);
        Route::delete('/category/{id}',[\App\Http\Controllers\CategoryController::class,'delete']);
        Route::get('/category',[\App\Http\Controllers\CategoryController::class,'allCategories']); //get all approved categories
            Route::get('/category/icon',[\App\Http\Controllers\CategoryIconController::class,'index']);
            Route::get('/subcategory',[\App\Http\Controllers\SubCategoryController::class,'allSubCategories']); //get all approved categories
//        CATEGORIES AND SUB CATEGORIES END

        // PRODUCT START
        Route::post('/product',[\App\Http\Controllers\ProductController::class,'store']);
        Route::get('/product',[\App\Http\Controllers\ProductController::class,'vendorProducts']);
        Route::put('/product/{product_id}',[\App\Http\Controllers\ProductController::class,'detailsEdit']);
        Route::post('/product/{product_id}/changeimage',[\App\Http\Controllers\ProductController::class,'changeProductImage']);
        // PRODUCT END
//        VARIATIONS START
        Route::post('/variation',[\App\Http\Controllers\VariationController::class,'store']);
        Route::delete('/variation/{id}',[\App\Http\Controllers\VariationController::class,'delete']);
        Route::get('/variation',[\App\Http\Controllers\VariationController::class,'all']);
        Route::get('/variation/primary',[\App\Http\Controllers\VariationController::class,'primary']);
        Route::get('/variation/secondary',[\App\Http\Controllers\VariationController::class,'secondary']);
//      <------------product variation---------->
        Route::post('/product/{product_id}/variation',[\App\Http\Controllers\ProductVariationController::class,'add']);
        Route::delete('/product/{product_id}/variation/{variation_id}',[\App\Http\Controllers\ProductVariationController::class,'delete']);
        Route::get('/product/{product_id}/variation/primary',[\App\Http\Controllers\ProductVariationController::class,'primary']);
        Route::get('/product/{product_id}/variation/secondary',[\App\Http\Controllers\ProductVariationController::class,'secondary']);
        Route::post('/product/{product_id}/variation/option',[\App\Http\Controllers\ProductVariatioOptionController::class,'store']);
        Route::get('/product/{product_id}/variation/option/primary',[\App\Http\Controllers\ProductVariatioOptionController::class,'primary']);
        Route::get('/product/{product_id}/variation/option/secondary',[\App\Http\Controllers\ProductVariatioOptionController::class,'secondary']);
        // VARIATIONS END
        // PRODUCT TAGS START
        Route::post('/product/{product_id}/tag',[\App\Http\Controllers\ProductTagController::class,'add']);
        Route::delete('/product/{product_id}/tag/{tag_id}',[\App\Http\Controllers\ProductTagController::class,'remove']);
        Route::get('/product/{product_id}/tag',[\App\Http\Controllers\ProductTagController::class,'index']);
        
        // PRODUCT TAGS END



        // PRODUCT STOCK START
        Route::post('/product/{product_id}/stock',[\App\Http\Controllers\ProductStockController::class,'store']);
        Route::get('/product/{product_id}/stock',[\App\Http\Controllers\ProductStockController::class,'index']);
        // PRODUCT STOCK END
        //    PRODUCT SPESIFICATIONS START
        Route::post('/product/{product_id}/specification',[\App\Http\Controllers\ProductSpecificationcontroller::class,'add']);
        Route::delete('/product/{product_id}/specification/{specification_id}',[\App\Http\Controllers\ProductSpecificationcontroller::class,'remove']);
        Route::get('/product/{product_id}/specification',[\App\Http\Controllers\ProductSpecificationcontroller::class,'index']);
        //    PRODUCT SPESIFICATIONS END

        //    PRODUCT FEATURES START
        Route::post('/product/{product_id}/feature',[\App\Http\Controllers\ProductFeatureController::class,'add']);
        Route::delete('/product/{product_id}/feature/{feature_id}',[\App\Http\Controllers\ProductFeatureController::class,'remove']);
        Route::get('/product/{product_id}/feature',[\App\Http\Controllers\ProductFeatureController::class,'index']);
        //    PRODUCT FEATURES END

    });
 
        Route::middleware(['authLess'])->group(function(){
            Route::post('/send-register-otp',[\App\Http\Controllers\VendorAuthController::class,'sendOtp']);
            Route::post('/send-login-otp',[\App\Http\Controllers\VendorAuthController::class,'sendLoginOtp']);
            Route::post('/register',[\App\Http\Controllers\VendorAuthController::class,'register']);
            Route::post('/login',[\App\Http\Controllers\VendorAuthController::class,'login']);
        });
});
//ADMIN
Route::prefix('/admin')->group(function (){
    Route::middleware(['auth:api','admin'])->group(function(){
        Route::post('/logout',[\App\Http\Controllers\AdminAuthcontroller::class,'logout']);
        Route::get('/notification',[\App\Http\Controllers\NotificationController::class,'notifications']);
        Route::delete('/notification/{id}',[\App\Http\Controllers\NotificationController::class,'delete']);
        Route::post('/read-notification/{id}',[\App\Http\Controllers\NotificationController::class,'readNotification']);
        Route::post('/unread-notification/{id}',[\App\Http\Controllers\NotificationController::class,'unReadNotification']);
        Route::post('/change-email-otp',[\App\Http\Controllers\SecurityController::class,'changeEmailOtp']);
        Route::post('/change-password',[\App\Http\Controllers\SecurityController::class,'changePassword']);
        Route::post('/change-email',[\App\Http\Controllers\SecurityController::class,'changeUserEmail']);
        
    });
    Route::middleware(['auth:api','admin','approvedAdmin'])->group(function(){
        Route::get('/data',[\App\Http\Controllers\AdminAuthcontroller::class,'data']);
//       approvement start
        Route::post('/vendor/approve/{vendor_id}',[\App\Http\Controllers\ApprovalController::class,'VendorApproval']);
        Route::post('/subcategory/approve/{id}',[\App\Http\Controllers\SubCategoryController::class,'approve']);
        Route::post('/subsubcategory/approve/{id}',[\App\Http\Controllers\SubSubCategoryController::class,'approve']);
        Route::post('/category/approve/{id}',[\App\Http\Controllers\CategoryController::class,'approve']);
        Route::post('/product/approve/{id}',[\App\Http\Controllers\ProductController::class,'approve']);
//        approvement end
        Route::get('/category/icon',[\App\Http\Controllers\CategoryIconController::class,'index']);
        Route::post('/category/icon',[\App\Http\Controllers\CategoryIconController::class,'add']);
        Route::delete('/category/icon/{id}',[\App\Http\Controllers\CategoryIconController::class,'delete']);
        Route::post('/category',[\App\Http\Controllers\CategoryController::class,'add']);
        Route::get('/subsubcategory',[\App\Http\Controllers\SubSubCategoryController::class,'index']); //get all approved categories
        Route::post('/subcategory',[\App\Http\Controllers\SubCategoryController::class,'add']);
        Route::delete('/subcategory/{id}',[\App\Http\Controllers\SubCategoryController::class,'delete']);
        Route::delete('/category/{id}',[\App\Http\Controllers\CategoryController::class,'delete']);
        Route::get('/category',[\App\Http\Controllers\CategoryController::class,'allCategories']); //get all approved categories
        Route::get('/subcategory',[\App\Http\Controllers\SubCategoryController::class,'allSubCategories']); //get all approved categories
        Route::post('/subsubcategory',[\App\Http\Controllers\SubSubCategoryController::class,'add']); //get all approved categories
        Route::delete('/subsubcategory/{id}',[\App\Http\Controllers\SubSubCategoryController::class,'delete']);
    });
    Route::middleware(['authLess'])->group(function(){
        Route::post('/send-register-otp',[\App\Http\Controllers\AdminAuthcontroller::class,'sendOtp']);
        Route::post('/send-login-otp',[\App\Http\Controllers\AdminAuthcontroller::class,'sendLoginOtp']);
        Route::post('/register',[\App\Http\Controllers\AdminAuthcontroller::class,'register']);
        Route::post('/login',[\App\Http\Controllers\AdminAuthcontroller::class,'login']);
    });
});
//CUSTOMER
Route::prefix('/customer')->group(function (){
    Route::get('/product',[\App\Http\Controllers\ProductController::class,'getAllProducts']);

    Route::middleware(['auth:api','customer'])->group(function(){
        Route::post('/logout',[\App\Http\Controllers\CustomerAuthController::class,'logout']);
        Route::get('/notification',[\App\Http\Controllers\NotificationController::class,'notifications']);
        Route::delete('/notification/{id}',[\App\Http\Controllers\NotificationController::class,'delete']);
        Route::post('/read-notification/{id}',[\App\Http\Controllers\NotificationController::class,'readNotification']);
        Route::post('/unread-notification/{id}',[\App\Http\Controllers\NotificationController::class,'unReadNotification']);
        Route::post('/change-email-otp',[\App\Http\Controllers\SecurityController::class,'changeEmailOtp']);
        Route::post('/change-password',[\App\Http\Controllers\SecurityController::class,'changePassword']);
        Route::post('/change-email',[\App\Http\Controllers\SecurityController::class,'changeUserEmail']);
        Route::post('/wishlist',[\App\Http\Controllers\WishListController::class,'add']);
        Route::get('/wishlist',[\App\Http\Controllers\WishListController::class,'index']);
        Route::delete('/wishlist/{product_id}',[\App\Http\Controllers\WishListController::class,'remove']);
        
    });
    Route::middleware(['authLess'])->group(function(){
        Route::post('/send-register-otp',[\App\Http\Controllers\CustomerAuthController::class,'sendOtp']);
        Route::post('/send-login-otp',[\App\Http\Controllers\CustomerAuthController::class,'sendLoginOtp']);
        Route::post('/register',[\App\Http\Controllers\CustomerAuthController::class,'register']);
        Route::post('/login',[\App\Http\Controllers\CustomerAuthController::class,'login']);
    });
});
