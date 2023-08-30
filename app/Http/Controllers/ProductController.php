<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductAddRequest;
use App\Models\Category;
use App\Models\Notification;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\UserDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    //
    public function addProduct(ProductAddRequest $request){
        if(Product::where('title',$request->title)->where('vendor_id',Auth::user()->id)->first() == null){
            $user = Auth::user();
            $category = Category::where('id',$request->category_id)->first();
            $subCategory = SubCategory::where('id',$request->subcategory_id)->first();
            if($category && $subCategory){
                $image = $request->file('product_image');
                $fileName = 'ecloset_product-img-'.random_int(1111,9999).time().'.'.$image->getClientOriginalExtension();
                $productCode ='v-'.Auth::user()->id.'-c'.substr($category,0,1).substr($category,-1).'-s'.substr($subCategory,0,2).substr($subCategory,-1).'-'.random_int(11,99);
                $product = Product::create([
                    'vendor_id'=>Auth::user()->id,
            'product_code'=> $productCode,
            'slug'=>Str::slug($request->title.' '.$productCode),
            'title'=>$request->title,
            'discription'=>$request->discription,
            'price'=>$request->price,
            'discount'=>$request->discount,
            'category_id'=>$request->category_id,
            'subcategory_id'=>$request->subcategory_id,
            'product_image'=>'/storage/images/'.$fileName,
                ]);
                $uploadStatus = $image->storeAs('images',$fileName,'public');

                if($product && $uploadStatus){
                    $ganderPerson = Str::lower(UserDetails::where('user_id',$user->id)->first()->gander) == 'male'?'his':'her';
                    $notification = Notification::create([
                        'notification_type_id'=>6,
                              'from_id'=>$user->id,
                              'receiver_id'=>null,
                              'receiver_role_id'=>3,
                              'ref_id'=>$product->id,
                              'tamplate'=>$user->firstName.' Created a product.Review product details and approve',
                            ]);

                    if($product && $notification){
                        return response()->json(['status'=>true,'message'=>'Product added successfully.!Please with for approval'],200);

                    }else{
                        return response()->json(['status'=>false,'message'=>'Something went wrong'],401);

                    }

                }else{
                    return response()->json(['status'=>false,'message'=>'Something went wrong'],401);

                }
            }else{
                return response()->json(['status'=>false,'message'=>'Something went wrong'],401);

            }
       
        }
 else{
     return response()->json(['status'=>false,'message'=>'You added this product'],401);
     
 }  
    
    }
    
    public function approveProduct($id){
       $product = Product::where('id',$id)->first();
        if($product){
            if($product->is_approved == false){
                $status = $product->update(['is_approved'=>true]);
                $notification = Notification::create([
                    'notification_type_id'=>7,
                              'from_id'=>Auth::user()->id,
                              'receiver_id'=>$product->vendor_id,
                              'receiver_role_id'=>2,
                              'ref_id'=>$product->id,
                              'tamplate'=>'Your product has been approved by an admin!'
                            ]);
                if($status && $notification){
                    return response()->json(['status'=>true,'message'=>'Product Approved!'],200);
                }else{
                    return response()->json(['status'=>false,'message'=>'Something went wrong!'],401);

                }
            }else{
                return response()->json(['status'=>false,'message'=>'Already approved!'],401);

            }
        }else{
            return response()->json(['status'=>false,'message'=>'No product Found!'],401);
            
        }
    }
}
