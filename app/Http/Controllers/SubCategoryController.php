<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCategoryRequest;
use App\Http\Requests\AddSubCategoryRequest;
use App\Models\Category;
use App\Models\Notification;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\UserDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
class SubCategoryController extends Controller
{
    //
    
    public function add(AddSubCategoryRequest $request){
  if(Category::where('id',$request->category_id)->first()){
      if(SubCategory::where('name',ucfirst($request->name))->first()){
          return response()->json(['status'=>false,'message'=>$request->name.'Already exists'],401);
      }else{
          if(SubCategory::where('slug',Str::slug($request->slug))->first()){
              return response()->json(['status'=>false,'message'=>$request->slug.'Already exists'],401);
          }else{
            if(Category::where('name',ucfirst($request->name))->first() == null){
                if(Auth::user()->user_role == 2){
                    $subCategory = SubCategory::create([
                        'category_id'=>$request->category_id,     
                'creator_id'=>Auth::user()->id,
                'name'=>ucfirst($request->name),
                'slug'=>Str::slug($request->slug),
           ]);
                    $ganderPerson = Str::lower(UserDetails::where('user_id',Auth::user()->id)->first()->gander) == 'male'?'his':'her';
                    $notification = Notification::create([
                        'notification_type_id'=>4,
                              'from_id'=>Auth::user()->id,
                              'receiver_id'=>null,
                              'receiver_role_id'=>3,
                              'ref_id'=>$subCategory->id,
                              'tamplate'=>Auth::user()->firstName.' Created a sub category.Review '.$ganderPerson.' sub category and approve',
                            ]);
                    if($subCategory && $notification){
                        return response()->json(['status'=>true,'message'=>'Successfully added '.ucfirst($request->name).'. Please wait for approval'],200);
                    }else{
                        return response()->json(['status'=>false,'message'=>'Something went wrong'],401);

                    }
                }else if (Auth::user()->user_role == 3){
                    $subCategory = SubCategory::create([
                        'category_id'=>$request->category_id,
                'creator_id'=>Auth::user()->id,
                'name'=>ucfirst($request->name),
                'slug'=>Str::slug($request->slug),
                'is_approved'=>true
           ]);

                    if($subCategory){
                        return response()->json(['status'=>true,'message'=>'Successfully added '.ucfirst($request->name)],200);
                    }else{
                        return response()->json(['status'=>false,'message'=>'Something went wrong'],401);

                    }
                }else{
                    return response()->json(['status'=>false,'message'=>'You are not permissible to add sub category'],401);

                }
            }else{
                return response()->json(['status'=>false,'message'=>'Change Sub category name'],401);
                
            }
          }
      }
  }else{
      return response()->json(['status'=>false,'message'=>'No category found with this id'],401);
      
  }
    }
    
    public function approve($id){
        $subCategory = SubCategory::where('id',$id)->first();
        if($subCategory->is_approved){
            return response()->json(['status'=>false,'message'=>'SubCategory already approved!'],401);
            
        }else{
            $status = $subCategory->update(['is_approved'=>true]);
            $notification = Notification::create([
                'notification_type_id'=>5,
                'from_id'=>Auth::user()->id,
                'receiver_id'=>$subCategory->creator_id,
                'receiver_role_id'=>2,
                'ref_id'=>$subCategory->id,
                'tamplate'=>'An admin approved your sub category..!Now you can add this sub category to your product',
          ]);
            if($status && $notification){
                return response()->json(['status'=>true,'message'=>'Sub Category approved!','data'=>$subCategory],200);
                
            }else{
                return response()->json(['status'=>false,'message'=>'Something went wrong!'],401);
                
            }
        }
    }
    public function delete($id){
        $subCategory = SubCategory::where('id',$id)->first();
        if($subCategory){
            if(Auth::user()->user_role == 3){
                if($subCategory->is_approved == false){
                    $subCategory->delete();
                    return response()->json(['status'=>true,'message'=>'Deleted!'],401);
                    
                }else{
                    if(Product::where('subcategory_id',$id)->first() == null){
                        $subCategory->delete();
                        return response()->json(['status'=>true,'message'=>'Deleted!'],200);
                        
                    }else{
                        return response()->json(['status'=>false,'message'=>'You can not delete sub category right now!'],401);
                        
                    }
                    
                }
            }else if (Auth::user()->user_role == 2){
                if($subCategory->creator_id == Auth::user()->id){
                    if($subCategory->is_approved == false){
                        $subCategory->delete();
                    }else{
                        return response()->json(['status'=>false,'message'=>'You can not delete sub category right now!'],401);

                    }
                }else{
                    return response()->json(['status'=>false,'message'=>'You are not permissible to delete sub category'],401);
                    
                }
            }else{
                return response()->json(['status'=>false,'message'=>'You are not permissible to delete sub category'],401);
                
            }
        }else{
            return response()->json(['status'=>false,'message'=>'No sub category found with this id!'],401);
            
        }
    }
    public function subCategories(){
        return response()->json(['status'=>true,'data'=>SubCategory::with('category')->get()->where('is_approved',true)],200);
    }
    
    public function allSubCategories(){
        return response()->json(['status'=>true,'data'=>SubCategory::with('category')->get()],200);
        
    }
}
