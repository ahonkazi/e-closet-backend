<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductImageUpdateRequest;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductEditRequest;
use App\Models\Category;
use App\Models\Notification;
use App\Models\Product;
use App\Models\ProductTag;
use App\Models\ProductVariationOption;
use App\Models\SubCategory;
use App\Models\Tag;
use App\Models\UserDetails;
use Illuminate\Support\Facades\File;
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
                        return response()->json(['status'=>false,'message'=>'Something went wrong'],500);

                    }
                }else{
                    return response()->json(['status'=>false,'message'=>'Product Already approved'],200);

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
    public function detailsEdit(ProductEditRequest $request,$product_id){
        $user_id = Auth::user()->id;
        $product = Product::where('id',$product_id)->where('vendor_id',$user_id)->first();
        if($product){
        $updatedProduct = $product->update([
        'title'=>$request->title,
        'discription'=>$request->discription,
      ]);
        if($updatedProduct){
            return response()->json(['status'=>true,'message'=>'Product Updated','data'=>$product],200);

        }else{
            return response()->json(['status'=>false,'message'=>'Something went wrong'],500);

        }
        }else{
            return response()->json(['status'=>false,'message'=>'Product Not found'],404);

        }
    }
    public function changeProductImage(ProductImageUpdateRequest $request,$product_id){
        $user_id = Auth::user()->id;
        $product = Product::where('id',$product_id)->where('vendor_id',$user_id)->first();
        if($product){
            $oldImagePath = $product->product_image;
            $fullpath = storage_path('app/public/'.explode('/storage/',$oldImagePath)[1]);
            $image = $request->file('product_image');
            $fileName = 'ecloset_pd_img'.random_int(1111,9999).time().'.'.$image->getClientOriginalExtension();
            $uploadStatus = $image->storeAs('images',$fileName,'public');
            if($uploadStatus){
                $status = $product->update([
                    'product_image'=>'/storage/images/'.$fileName
                ]);
                if($status){
                      unlink($fullpath);
                      return response()->json(['status'=>true,'message'=>'updated image','data'=>$product],200);

                }else{
                    return response()->json(['status'=>false,'message'=>'Something went wrong'],500);

                }
            }else{
                return response()->json(['status'=>false,'message'=>'Something went wrong'],500);

            }
        }else{
            return response()->json(['status'=>false,'message'=>'Product Not found'],404);

        }
    }

    public function getAllProducts(){
        $products = Product::with('productStock','category','sub_category','sub_sub_category')->get();
        return response()->json(['data'=>$products],200);
    }

    public function search(Request $request){
        $query = Product::query()->where('is_approved',1);
        if($request->has('q')){
            $query->where('title','like','%'.$request->q.'%')->orWhere('discription','like','%'.$request->q.'%');
        }
        if ($request->has('category_ids')) {
             $categories = $request->category_ids;
             $query->whereIn('category_id', $categories);
        }
          if ($request->has('tag_ids')) {
              $tags = Tag::all()->whereIn('id',$request->tag_ids);
              $tagIds = [];
              foreach ($tags as $tag){
                  array_push($tagIds,$tag->id);
              }
              $productTagIds = [];
              $productTags = ProductTag::all()->whereIn('tag_id',$tagIds);
                foreach ($productTags as $productTag){
                    array_push($productTagIds,$productTag->id);
                }
              $query->whereHas('product_tags',function ($q) use ($productTagIds){
                  $q->whereIn('id',$productTagIds);
              });
          }

          if ($request->has('price_min')) {
              $price = $request->price_min;
              $query->whereHas('productStock',function ($q) use ($price){
                  $q->where('price','>=',$price);
              });
          }
           if ($request->has('price_max')) {
               $price = $request->price_max;
               $query->whereHas('productStock',function ($q) use ($price){
                   $q->where('price','<=',$price);
               });
           }

    if($request->has('size_values')){
        $sizeValues = $request->size_values;
        $size_idList =ProductVariationOption::whereIn('value',$sizeValues)->get(['id']);
        $size_ids = [];
        foreach ($size_idList as $id){
            array_push($size_ids,$id->id);
        }
        $query->whereHas('productStock',function ($q) use ($size_ids){
            $q->whereIn('primary_option_id',$size_ids);
        });
    }


    if($request->has('model_values')){
        $sizeValues = $request->model_values;
        $size_idList =ProductVariationOption::whereIn('value',$sizeValues)->get(['id']);
        $size_ids = [];
        foreach ($size_idList as $id){
            array_push($size_ids,$id->id);
        }
        $query->whereHas('productStock',function ($q) use ($size_ids){
            $q->whereIn('primary_option_id',$size_ids);
        });
    }

     if($request->has('color_values')){
         $colorValues = $request->color_values;
         $color_idList =ProductVariationOption::whereIn('value',$colorValues)->get(['id']);
         $color_ids = [];
         foreach ($color_idList as $id){
             array_push($color_ids,$id->id);
         }
        $query->whereHas('productStock',function ($q) use ($color_ids){
            $q->whereIn('secondary_option_id',$color_ids);
        });
     }

                  if ($request->has('stock_min')) {
                      $stock_min = $request->stock_min;
                      $query->whereHas('productStock',function ($q) use ($stock_min){
                          $q->where('stock','<=',$stock_min);
                      });
                  }
                        if ($request->has('stock_max')) {
                            $stock_max = $request->stock_max;
                            $query->whereHas('productStock',function ($q) use ($stock_max){
                                $q->where('stock','<=',$stock_max);
                            });
                        }
        $per_page = $request->input('per_page',10);
        $products = $query->paginate($per_page);
        return response()->json($products,200);
    }
}
