<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductVariationOptionStoreRequest;
use App\Models\DisplayVariation;
use App\Models\DisplayVariationOption;
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
        $variation = Variation::where('id',$product_variation->variation_id)->first();
        if($variation == null){
            return response()->json(['status'=>false,'message'=>'No variation found!'],500);
        }
        if($product_variation->is_primary){
            $displayOption = DisplayVariationOption::where('value',$request->value)->first();
            if($displayOption){
                $check = ProductVariationOption::where('product_id',$product_id)->where('vendor_id',$user->id)->where('product_variation_id',$request->product_variation_id)->where('value',$request->value)->first();
              if($check){
                  return response()->json(['status'=>false,'message'=>'Already exists!'],500);

              }else{
                  $option = ProductVariationOption::create([
                      'product_id'=>$product_id,
                'vendor_id'=>$user->id,
                'product_variation_id'=>$request->product_variation_id,
                'value'=>$displayOption->value,
                'is_primary'=>true,
                'display_variation_option_id'=>$displayOption->id
             ]);
                  if($option){
                      return response()->json(['status'=>true,'data'=>$option],200);

                  }else{
                      return response()->json(['status'=>false,'message'=>'Something went wrong!'],500);

                  }
              }
            }
        else{
            $displayVariation = DisplayVariation::where('name',$variation->name)->first();
            $newDisplayOption = DisplayVariationOption::create(['value'=>$request->value,'display_variation_id'=>$displayVariation->id]);
            $option = ProductVariationOption::create([
                'product_id'=>$product_id,
                'vendor_id'=>$user->id,
                'product_variation_id'=>$request->product_variation_id,
                'value'=>$newDisplayOption->value,
                'is_primary'=>true,
                'display_variation_option_id'=>$newDisplayOption->id
             ]);
            if($option){
                return response()->json(['status'=>true,'data'=>$option],200);

            }else{
                return response()->json(['status'=>false,'message'=>'Something went wrong!'],500);

            }


            }
        }else{
            $displayOption = DisplayVariationOption::where('value',$request->value)->first();
            $colorCode = $request->color_code;
            $image = $request->file('product_image');
            if($colorCode && $image){
                $fileName = 'ecloset_pd_img-'.random_int(1111,9999).time().'.'.$image->getClientOriginalExtension();
                $uploadStatus = $image->storeAs('images',$fileName,'public');
                if($displayOption){
                    $check = ProductVariationOption::where('product_id',$product_id)->where('vendor_id',$user->id)->where('product_variation_id',$request->product_variation_id)->where('value',$request->value)->first();
if($check){
    return response()->json(['status'=>false,'message'=>'Already exists!'],500);

}          else{
    $option = ProductVariationOption::create([
        'product_id'=>$product_id,
                'vendor_id'=>$user->id,
                'product_variation_id'=>$request->product_variation_id,
                'value'=>$displayOption->value,
                'is_primary'=>false,
                'color_code'=>$request->color_code,
                'product_image'=>'/storage/images/'.$fileName,
                'display_variation_option_id'=>$displayOption->id

             ]);
    if($uploadStatus && $option){
        return response()->json(['status'=>true,'data'=>$option],200);

    }else{
        return response()->json(['status'=>false,'message'=>'Something went wrong!'],500);

    }
}


                }else{
                    $displayVariation = DisplayVariation::where('name',$variation->name)->first();
                    $newDisplayOption = DisplayVariationOption::create(['value'=>$request->value,'display_variation_id'=>$displayVariation->id,'color_code'=>$request->color_code]);
                    $option = ProductVariationOption::create([
                'product_id'=>$product_id,
                'vendor_id'=>$user->id,
                'product_variation_id'=>$request->product_variation_id,
                'value'=>$newDisplayOption->value,
                'is_primary'=>false,
                'color_code'=>$request->color_code,
                'product_image'=>'/storage/images/'.$fileName,
                'display_variation_option_id'=>$newDisplayOption->id

             ]);
                    if($uploadStatus && $option){
                        return response()->json(['status'=>true,'data'=>$option],200);

                    }else{
                        return response()->json(['status'=>false,'message'=>'Something went wrong!'],500);

                    }

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
