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
    
    
    public function store(ProductStockStoreRequest $request,$id){
        $product = Product::where('id',$id)->where('vendor_id',Auth::user()->id)->first();
        if($product){
            $primaryOption = ProductVariationOption::where('id',$request->primary_option_id)->where('product_id',$product->id)->where('vendor_id',Auth::user()->id)->first();
            $secondaryOption = ProductVariationOption::where('id',$request->secondary_option_id)->where('product_id',$product->id)->where('vendor_id',Auth::user()->id)->first();
            $primaryVariation = ProductVariation::where('id',$primaryOption->product_variation_id)->where('product_id',$product->id)->first(); 
            $secondaryVariation = ProductVariation::where('id',$secondaryOption->product_variation_id)->where('product_id',$product->id)->first(); 
            
            if($request->secondary_option_id == null){
                if($primaryVariation->is_primary == true){
                    if(ProductStock::where('product_id',$product->id)->where('primary_option_id',$request->primary_option_id)->where('vendor_id',Auth::user()->id)->first()){
                        return response()->json(['status'=>false,'message'=>'Stock exists']);       
                    }else{
                        $image = $request->file('image');
                        $fileName = 'ecloset_pd_img'.random_int(1111,9999).time().'.'.$image->getClientOriginalExtension();
                        $uploadStatus = $image->storeAs('images',$fileName,'public');
                        $stock = ProductStock::create([
                            'product_id'=>$product->id,
                            'vendor_id'=>Auth::user()->id,
                            'primary_option_id'=>$request->primary_option_id,
                            'image'=>'/storage/images/'.$fileName,
                            'price'=>$request->price,
                            'discount_in_percent'=>$request->discount_in_percent,
                            'stock'=>$request->stock
                        ]);
                        if($uploadStatus && $stock){
                            return response()->json(['status'=>true,'message'=>'Stock created','data'=>$stock],200);       
                            
                        }else{
                            return response()->json(['status'=>false,'message'=>'Invalid primary variation '],401);       
                            
                        }
                    }
                }else{
                    return response()->json(['status'=>false,'message'=>'Something went wrong'],401);       
                    
                }
            }else{
                if($primaryVariation->is_primary == true && $secondaryVariation->is_primary == false ){
                    if(ProductStock::where('product_id',$product->id)->where('primary_option_id',$request->primary_option_id)->where('secondary_option_id',$request->secondary_option_id)->where('vendor_id',Auth::user()->id)->first()){
                        return response()->json(['status'=>false,'message'=>'Stock exists']);       
                        
                    }else{
                        $image = $request->file('image');
                        $fileName = 'ecloset_pd_img'.random_int(1111,9999).time().'.'.$image->getClientOriginalExtension();
                        $uploadStatus = $image->storeAs('images',$fileName,'public');
                        $stock = ProductStock::create([
                            'product_id'=>$product->id,
                            'vendor_id'=>Auth::user()->id,
                            'primary_option_id'=>$request->primary_option_id,
                            'secondary_option_id'=>$request->secondary_option_id,
                            'image'=>'/storage/images/'.$fileName,
                            'price'=>$request->price,
                            'discount_in_percent'=>$request->discount_in_percent,
                            'stock'=>$request->stock
                        ]);
                        if($uploadStatus && $stock){
                            return response()->json(['status'=>true,'message'=>'Stock created','data'=>$stock],200);       

                        }else{
                            return response()->json(['status'=>false,'message'=>'Something went wrong'],401);       

                        }
                    }
                }else{
                        return response()->json(['status'=>false,'message'=>'Invalid primary or secondary variation '],401);      
                
                }
            }
 
            
        }else{
            return response()->json(['status'=>false,'message'=>'Product not found!'],404);       
            
        }
 
  
    }
    
    public function index($id){
        $product = Product::where('id',$id)->where('vendor_id',Auth::user()->id)->first();
        if($product){
            return response()->json(['status'=>true,'data'=>ProductStock::with('primary_option:id,value','secondary_option:id,value')->where('product_id',$product->id)->where('vendor_id',Auth::user()->id)->get()],200);
        }        
    }
}
