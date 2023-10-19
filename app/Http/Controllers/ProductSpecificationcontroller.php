<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductSpecificationAddRequest;
use App\Models\Product;
use App\Models\Specification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductSpecificationcontroller extends Controller
{
    //
    
    public function remove($product_id,$specification_id){
        $vendor = Auth::user(); 
        $product = Product::where('id',$product_id)->where('vendor_id',$vendor->id)->first();
        if($product){
            $specification = Specification::where('id',$specification_id)->where('vendor_id',$vendor->id)->where('product_id',$product_id)->first();
            if($specification){
            $staus = $specification->delete();
if($staus){
    return response()->json(['status'=>true,'message'=>'deleted'],200);     
    
}else{
    return response()->json(['status'=>false,'message'=>'Something went wrong'],500);     
    
}            
            }else{
                return response()->json(['status'=>false,'message'=>'Specification Not found'],404);     
                
            }
         
        }else{
            return response()->json(['status'=>false,'message'=>'Product Not found'],404);     
            
        }
    }
    
        public function add(ProductSpecificationAddRequest $request,$product_id){
        $vendor = Auth::user(); 
        $product = Product::where('id',$product_id)->where('vendor_id',$vendor->id)->first();
        if($product){
            $specification = Specification::where('product_id',$product_id)->where('vendor_id',$vendor->id)->where('name',$request->name)->first();
            if($specification){
                return response()->json(['status'=>false,'message'=>'Already exists'],403);     
            }else{
                $NewSpecification = Specification::create([
                    'product_id'=>$product_id,
            'vendor_id'=>$vendor->id,
            'name'=>$request->name,
            'value'=>$request->value
        ]);
                if($NewSpecification){
                    return response()->json(['status'=>true,'message'=>'Added','data'=>$NewSpecification],200);     

                }else{
                    return response()->json(['status'=>false,'message'=>'Something went wrong'],500);     

                }
            }

        }else{
            return response()->json(['status'=>false,'message'=>'Product Not found'],404);     
            
        }
    }
    
    public function index($product_id){
        $vendor = Auth::user(); 
        $product = Product::where('id',$product_id)->where('vendor_id',$vendor->id)->first();
        $data = Specification::all()->where('product_id',$product_id)->where('vendor_id',$vendor->id);
        if($product){
            return response()->json(['status'=>true,'data'=>$data],200);
        }else{
            return response()->json(['status'=>false,'message'=>'Product Not found'],404);     
            
        }
    }

}
