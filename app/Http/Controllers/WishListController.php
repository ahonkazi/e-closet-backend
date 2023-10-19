<?php

namespace App\Http\Controllers;

use App\Http\Requests\WishListAddRequest;
use App\Models\Product;
use App\Models\WishList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishListController extends Controller
{
    //
    
    public function add(WishListAddRequest $request){
        $product = Product::where('id',$request->product_id)->first();
        $user_id = Auth::user()->id;
        if($product){
            $wishlist = WishList::where('customer_id',$user_id)->where('product_id',$request->product_id)->first();
            if($wishlist){
                return response()->json(['status'=>false,'message'=>'Already Exists'],500);
            }else{
                $newWishlist = WishList::create(['product_id'=>$request->product_id,'customer_id'=>$user_id]);
                if($newWishlist){
                    return response()->json(['status'=>true,'message'=>'Added','data'=>$newWishlist],200);
                    
                }else{
                    return response()->json(['status'=>false,'message'=>'Something went wrong'],500);
                }
            }
        }else{
            return response()->json(['status'=>false,'message'=>'Product not found'],404);
            
        }
    }
    
    public function remove($product_id){
        $product = Product::where('id',$product_id)->first();
        $user_id = Auth::user()->id;
        if($product){
            $wishlist = WishList::where('customer_id',$user_id)->where('product_id',$product_id)->first();
            if($wishlist){
                    $deleteStatus = $wishlist->delete();
                    if($deleteStatus){
                        return response()->json(['status'=>true,'message'=>'removed'],200);
                        
                    }else{
                        return response()->json(['status'=>false,'message'=>'Something went wrong'],500);
                        
                    }
            }else{
                return response()->json(['status'=>false,'message'=>'not exists'],404);
                
            }
        }else{
            return response()->json(['status'=>false,'message'=>'Product not found'],404);
            
        }
    }
    
    public function index(){
        $user_id = Auth::user()->id;
        $wishlist = WishList::with('product.productStock')->where('customer_id',$user_id)->get()->sortBy('DESC');
        return response()->json(['status'=>true,'data'=>$wishlist],200);
        
    }
}
