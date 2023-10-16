<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductVariationOptionStoreRequest;
use App\Models\Product;
use App\Models\ProductVariationOption;
use App\Models\ProductVariation;
use App\Models\Variation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductVariatioOptionController extends Controller
{
    //
    
public function store(ProductVariationOptionStoreRequest $request,$product_id){
    $user = Auth::user();
    $product = Product::where('id',$product_id)->where('vendor_id',$user->id)->first();
    $product_variation = ProductVariation::where('id',$request->product_variation_id)->where('vendor_id',$user->id)->where('product_id',$product_id)->first();
    if($product && $product_variation){
        if($product_variation->is_primary){
            $option = ProductVariationOption::create([
                'product_id'=>$product_id,
                'vendor_id'=>$user->id,
                'product_variation_id'=>$request->product_variation_id,
                'value'=>$request->value,
                'is_primary'=>true
             ]);
            if($option){
                return response()->json(['status'=>true,'data'=>$option],200);

            }else{
                return response()->json(['status'=>false,'message'=>'Something went wrong!'],83);

            }
        }else{
            $colorCode = $request->color_code;
            $image = $request->file('product_image');
            if($colorCode && $image){
                $fileName = 'ecloset_pd_img-'.random_int(1111,9999).time().'.'.$image->getClientOriginalExtension();
                $uploadStatus = $image->storeAs('images',$fileName,'public');
                $option = ProductVariationOption::create([
                'product_id'=>$product_id,
                'vendor_id'=>$user->id,
                'product_variation_id'=>$request->product_variation_id,
                'value'=>$request->value,
                'is_primary'=>false,
                'color_code'=>$request->color_code,
                'product_image'=>'/storage/images/'.$fileName,
             ]);
                if($uploadStatus && $option){
                    return response()->json(['status'=>true,'data'=>$option],200);

                }else{
                    return response()->json(['status'=>false,'message'=>'Something went wrong!'],83);

                }
            }else{
                if($colorCode){
                    return response()->json(['status'=>false,'message'=>'Select product_image'],401);

                }elseif ($image){
                    return response()->json(['status'=>false,'message'=>'Select color_code'],401);

                }else{
                    return response()->json(['status'=>false,'message'=>'Select color_code and product_image'],401);

                }

            }

        }
    }else{
        return response()->json(['status'=>false,'message'=>'Not Found'],404);

    }
}

  public function primary($product_id){
    $data = ProductVariationOption::all()->where('product_id',$product_id)->where('vendor_id',Auth::user()->id)->where('is_primary',1);
    if($data){
        return response()->json(['status'=>true,'data'=>$data],200);
    }else{
        return response()->json(['status'=>false,'message'=>'Not found'],404);
    }
}
 public function secondary($product_id){
    $data = ProductVariationOption::all()->where('product_id',$product_id)->where('vendor_id',Auth::user()->id)->where('is_primary',0);
    if($data){
        return response()->json(['status'=>true,'data'=>$data],200);
    }else{
        return response()->json(['status'=>false,'message'=>'Not found'],404);
    }
}
}
