<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductTagAddRequest;
use App\Models\Product;
use App\Models\ProductTag;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProductTagController extends Controller
{
    //
    
    public function add(ProductTagAddRequest $request,$product_id){
        $user = Auth::user();
        $product = Product::where('id',$product_id)->where('vendor_id',$user->id)->first();
        if($product){
            $tag = Tag::where('name',Str::lower($request->name))->first();
            if($tag){
                $TagExist = ProductTag::where('tag_id',$tag->id)->where('product_id',$product_id)->where('vendor_id',$user->id)->first();
                if($TagExist){
                    return response()->json(['status'=>false,'message'=>'Already Exists'],403);
                }else{
                    $productTag = ProductTag::create(['tag_id'=>$tag->id,'product_id'=>$product_id,'vendor_id'=>$user->id]);
                    if($productTag){
                        return response()->json(['status'=>true,'message'=>'added '.$request->name,'data'=>$productTag],200);
                    }else{
                        return response()->json(['status'=>false,'message'=>'Something went wrong'],83);
                    }        
                }
            
            }else{
                $newTag = Tag::create(['name'=>Str::lower($request->name),'slug'=>Str::slug($request->name)]);
                if($newTag){
                    $productTag = ProductTag::create(['tag_id'=>$newTag->id,'product_id'=>$product_id,'vendor_id'=>$user->id]);
                    if($productTag){
                        return response()->json(['status'=>true,'message'=>'added '.$request->name,'data'=>$productTag],200);
                    }else{
                        return response()->json(['status'=>false,'message'=>'Something went wrong'],83);
                    } 
                }else{
                    return response()->json(['status'=>false,'message'=>'Something went wrong'],83);
                    
                }

            }
        }else{
            return response()->json(['status'=>false,'message'=>'No Product Found'],404);
            
        }
    }
    public function remove($product_id,$tag_id){
        $user = Auth::user();
        $product = Product::where('id',$product_id)->where('vendor_id',$user->id)->first();
        if($product){
            $productTag = ProductTag::where('id',$tag_id)->where('product_id',$product_id)->where('vendor_id',$user->id)->first();            
            if($productTag){
                $status =  $productTag->delete();
                if($status){
                    return response()->json(['status'=>true,'message'=>'removed'],200);
                    
                }else{
                    return response()->json(['status'=>false,'message'=>'Something went wrong'],83);
                    
                }
            }else{
                return response()->json(['status'=>false,'message'=>'No Product Tag Found'],404);
                
            }
        
        }else{
            return response()->json(['status'=>false,'message'=>'No Product Found'],404);
            
        }
    }
    public function index($product_id){
        $user = Auth::user();
        $product = Product::where('id',$product_id)->where('vendor_id',$user->id)->first();
        if($product){
            $data = ProductTag::with('tag')->where('product_id',$product_id)->where('vendor_id',$user->id)->get();            
            return response()->json(['status'=>true,'data'=>$data],200);
        }else{
            return response()->json(['status'=>false,'message'=>'No Product Found'],404);
            
        }
    }
}
