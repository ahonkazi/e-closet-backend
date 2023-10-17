<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductFeatureAddRequest;
use App\Models\Product;
use App\Models\ProductFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductFeatureController extends Controller
{
    //
   
    public function add(ProductFeatureAddRequest $request,$product_id){
        $vendor = Auth::user(); 
        $product = Product::where('id',$product_id)->where('vendor_id',$vendor->id)->first();
        if($product){
        $feature = ProductFeature::where('item',$request->item)->where('product_id',$product_id)->where('vendor_id',$vendor->id)->first();
        if($feature){
            return response()->json(['status'=>false,'message'=>'Already exists'],403);     
        }else{
            $newFeature = ProductFeature::create([
        'product_id'=>$product_id,
        'vendor_id'=>$vendor->id,
        'item'=>$request->item
            ]);
            if($newFeature){
                return response()->json(['status'=>true,'message'=>'Added','data'=>$newFeature],200);     
                
            }else{
                    return response()->json(['status'=>false,'message'=>'Something went wrong'],83);     
                }     
        }
        }else{
            return response()->json(['status'=>false,'message'=>'Product Not found'],404);     
        }
            
    }
    
//    remove

    public function remove($product_id,$feature_id){
        $vendor = Auth::user(); 
        $product = Product::where('id',$product_id)->where('vendor_id',$vendor->id)->first();
        if($product){
            $feature = ProductFeature::where('id',$feature_id)->where('vendor_id',$vendor->id)->where('product_id',$product_id)->first();
            if($feature){
                $staus = $feature->delete();
                if($staus){
                    return response()->json(['status'=>true,'message'=>'deleted'],200);     

                }else{
                    return response()->json(['status'=>false,'message'=>'Something went wrong'],83);     

                }            
            }else{
                return response()->json(['status'=>false,'message'=>'Feature Not found'],404);     

            }

        }else{
            return response()->json(['status'=>false,'message'=>'Product Not found'],404);     

        }
    }
    
//    get data
    public function index($product_id){
        $vendor = Auth::user(); 
        $product = Product::where('id',$product_id)->where('vendor_id',$vendor->id)->first();
        $data = ProductFeature::all()->where('product_id',$product_id)->where('vendor_id',$vendor->id);
        if($product){
            return response()->json(['status'=>true,'data'=>$data],200);
        }else{
            return response()->json(['status'=>false,'message'=>'Product Not found'],404);     

        }
    }
    
}
