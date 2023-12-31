<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStockStoreRequest;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ProductVariation;
use App\Models\ProductVariationOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductStockController extends Controller
{
    //
    
public function store(ProductStockStoreRequest $request,$product_id){
    $user = Auth::user();
    $product = Product::where('id',$product_id)->where('vendor_id',$user->id)->first();
    if($product){
        $primaryOption = ProductVariationOption::where('id',$request->primary_option_id)->where('vendor_id',$user->id)->where('is_primary',1)->first();
        $secondaryOption = ProductVariationOption::where('id',$request->secondary_option_id)->where('vendor_id',$user->id)->where('is_primary',0)->first();
        if($primaryOption){
            if($secondaryOption){
                $stock = ProductStock::where('primary_option_id',$request->primary_option_id)->where('secondary_option_id',$request->secondary_option_id)->where('vendor_id',$user->id)->first();
                if($stock){
                    return response()->json(['status'=>false,'message'=>'Already exists'],403);

                }else{
                    $newStock = ProductStock::create([
                        'product_id'=>$product_id,
                        'vendor_id'=>$user->id,
                        'primary_option_id'=>$request->primary_option_id,
                        'secondary_option_id'=>$request->secondary_option_id,
                        'price'=>$request->price,
                        'discount_in_percent'=>$request->discount_in_percent,
                        'stock'=>$request->stock
                    ]);
                    if($newStock){
                        return response()->json(['status'=>true,'message'=>'Stock Created','data'=>$newStock],200);

                    }else{
                        return response()->json(['status'=>false,'message'=>'Something went srong'],500);

                    }
                }
            }else{
                $stock = ProductStock::where('primary_option_id',$request->primary_option_id)->where('vendor_id',$user->id)->first();
                if($stock){
                    return response()->json(['status'=>false,'message'=>'Already exists'],403);

                }else{
                    $newStock = ProductStock::create([
                        'product_id'=>$product_id,
                        'vendor_id'=>$user->id,
                        'primary_option_id'=>$request->primary_option_id,
                        'price'=>$request->price,
                        'discount_in_percent'=>$request->discount_in_percent,
                        'stock'=>$request->stock
                    ]);
                    if($newStock){
                        return response()->json(['status'=>true,'message'=>'Stock Created','data'=>$newStock],200);

                    }else{
                        return response()->json(['status'=>false,'message'=>'Something went srong'],500);

                    }
                }
            }
        }else{
            return response()->json(['status'=>false,'message'=>'Primary Option not found'],404);

        }
    }else{
        return response()->json(['status'=>false,'message'=>'No Product Found'],404);
    }
}
    public function index($product_id){
    $product = Product::where('id',$product_id)->where('vendor_id',Auth::user()->id)->first();
        if($product){
            return response()->json(['status'=>true,'data'=>ProductStock::with('primary_option:id,value','secondary_option:id,value,color_code,product_image')->where('product_id',$product->id)->where('vendor_id',Auth::user()->id)->get()],200);
        }        
    }

   public function delete($product_id,$stock_id){
    $user = Auth::user();
    $product = Product::where('id',$product_id)->where('vendor_id',$user->id)->first();
    if($product){
        $stock = ProductStock::where('id',$stock_id)->where('product_id',$product_id)->where('vendor_id',$user->id)->first();
    if($stock){
            $status = $stock->delete();
            if($status){
                return response()->json(['status'=>false,'message'=>'Deleted'],200);

            }else{
                return response()->json(['status'=>false,'message'=>'Something went wrong'],500);

            }
    }else{
        return response()->json(['status'=>false,'message'=>'No Stock Found'],404);

    }
    }else{
        return response()->json(['status'=>false,'message'=>'No Product Found'],404);

    }
}
}
