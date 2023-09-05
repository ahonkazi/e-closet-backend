<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
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
    public function store(ProductStoreRequest $request){
        if(Product::where('title',$request->title)->where('category_id',$request->category_id)->where('sub_category_id',$request->sub_category_id)->where('vendor_id',Auth::user()->id)->first() == null){
            $category = Category::where('id',$request->category_id)->first()->name;
            $subCtg = SubCategory::where('id',$request->sub_category_id)->first()->name;
            $image = $request->file('product_image');
            $fileName = 'ecloset_pdimg'.random_int(1111,9999).time().'.'.$image->getClientOriginalExtension();
            $uploadStatus = $image->storeAs('images',$fileName,'public');
            $product = Product::create([
       'vendor_id'=>Auth::user()->id,
       'title'=>$request->title,
       'discription'=>$request->discription,
       'code'=>'p'.random_int(1111,9999).substr($category,0,2).substr($subCtg,0,2).substr($request->title,0,4),
       'slug'=>Str::slug($category.' '.$subCtg.' '.$request->title),
       'category_id'=>$request->category_id,
       'sub_category_id'=>$request->sub_category_id,
       'sub_sub_category_id'=>$request->sub_sub_category_id,
       'product_image'=>'/storage/images/'.$fileName
    ]);
            $ganderPerson = Str::lower(UserDetails::where('user_id',Auth::user()->id)->first()->gander) == 'male'?'his':'her';

            $notification = Notification::create([
                              'notification_type_id'=>6,
                              'from_id'=>Auth::user()->id,
                              'receiver_id'=>null,
                              'receiver_role_id'=>3,
                              'ref_id'=>$product->id,
                              'tamplate'=>Auth::user()->firstName.' added a product.Review '.$ganderPerson.' product and approve',
                            ]);
        if($product && $uploadStatus && $notification){
            return response()->json(['status'=>true,'message'=>'Product added successfully,please wait a moment for approval.!'],200);

        }else{
            return response()->json(['status'=>false,'message'=>'Something went wrong'],401);

        }
        }else{
            return response()->json(['status'=>false,'message'=>'Product Already added'],401);
        }
    }

    public function approve($id){
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
                              'tamplate'=>'An Admin approved your product.'
                            ]);
                    if($status && $notification){
                        return response()->json(['status'=>true,'message'=>'Product Approved!','data'=>$product],200);

                    }else{
                        return response()->json(['status'=>false,'message'=>'Something went wrong'],401);

                    }
                }else{
                    return response()->json(['status'=>false,'message'=>'Product Already added'],401);

                }
        }else{
            return response()->json(['status'=>false,'message'=>'No product found'],404);
            
        }
    }
    public function vendorProducts(){
        $user_id = Auth::user()->id;
        $data = Product::with('category','sub_category','sub_sub_category')->where('vendor_id',$user_id)->get();
        return response()->json(['status'=>true,'total_product'=>count($data),'data'=>$data]);
    }
}
