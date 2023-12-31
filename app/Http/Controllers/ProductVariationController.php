<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductVariationAddRequest;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ProductVariationOption;
use App\Models\Variation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductVariationController extends Controller
{
    //
    public function add(ProductVariationAddRequest $request,$product_id){
        $user = Auth::user();
        $product = Product::where('id',$product_id)->where('vendor_id',$user->id)->first();
        $variation = Variation::where('id',$request->variation_id)->where('vendor_id',$user->id)->first();
        if ($product && $variation){
            $productVariation = ProductVariation::create([
                'product_id'=>$request->product_id,
                'variation_id'=>$request->variation_id,
                'vendor_id'=>$user->id,
                'is_primary'=>$variation->is_primary
    ]);
            if($productVariation){
                return response()->json(['status'=>true,'message'=>'Product Variation Added','data'=>$productVariation],200);
            }
        }else{
            return response()->json(['status'=>false,'message'=>'Invalid Product Variation'],404);
            
        }
    }
    
    public function primary($product_id){
        $data = ProductVariation::with('variation')->where('product_id',$product_id)->where('vendor_id',Auth::user()->id)->where('is_primary',1)->first();
           return response()->json(['status'=>true,'data'=>$data],200);

    }
     public function secondary($product_id){
        $data = ProductVariation::with('variation')->where('product_id',$product_id)->where('vendor_id',Auth::user()->id)->where('is_primary',0)->first();

            return response()->json(['status'=>true,'data'=>$data],200);

    }

    public function delete($product_id,$variation_id){
        $user = Auth::user();
        $product = Product::where('id',$product_id)->where('vendor_id',$user->id)->first();
if($product){
    $variation = ProductVariation::where('id',$variation_id)->where('vendor_id',$user->id)->where('product_id',$product_id)->first();
 if($variation){
     $options = ProductVariationOption::where('product_variation_id',$variation->id)->first();
     if($options){
         return response()->json(['status'=>false,'message'=>'You can not delete'],500);

     }else{
         $variation->delete();
         return response()->json(['status'=>true,'message'=>'deleted'],200);
     }
 }else{
     return response()->json(['status'=>false,'message'=>'Variation Not found'],404);

 }
}else{
    return response()->json(['status'=>false,'message'=>'Product Not found'],404);

}
    }
}
