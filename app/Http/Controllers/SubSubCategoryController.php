<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCategoryRequest;
use App\Http\Requests\AddSubCategoryRequest;
use App\Http\Requests\SubSubCategoryStorRequest;
use App\Models\Category;
use App\Models\Notification;
use App\Models\SubCategory;
use App\Models\SubSubCategory;
use App\Models\UserDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
class SubSubCategoryController extends Controller
{
    //
    
    public function add(SubSubCategoryStorRequest $request){
  if(SubCategory::where('id',$request->subcategory_id)->first()){
      if(SubSubCategory::where('name',ucfirst($request->name))->first()){
          return response()->json(['status'=>false,'message'=>$request->name.'Already exists'],401);
      }else{
          if(SubSubCategory::where('slug',Str::slug($request->slug))->first()){
              return response()->json(['status'=>false,'message'=>$request->slug.'Already exists'],401);
          }else{
            if(Category::where('name',ucfirst($request->name))->first() == null){
                if(Auth::user()->user_role == 2){
                    $subSubCategory = SubSubCategory::create([
                 'sub_category_id'=>$request->subcategory_id,
                'creator_id'=>Auth::user()->id,
                'name'=>ucfirst($request->name),
                'slug'=>Str::slug($request->slug),
           ]);
                    $ganderPerson = Str::lower(UserDetails::where('user_id',Auth::user()->id)->first()->gander) == 'male'?'his':'her';
                    $notification = Notification::create([
                        'notification_type_id'=>8,
                              'from_id'=>Auth::user()->id,
                              'receiver_id'=>null,
                              'receiver_role_id'=>3,
                              'ref_id'=>$subSubCategory->id,
                              'tamplate'=>Auth::user()->firstName.' Created a sub sub category.Review '.$ganderPerson.' sub category and approve',
                            ]);
                    if($subSubCategory && $notification){
                        return response()->json(['status'=>true,'message'=>'Successfully added '.ucfirst($request->name).'. Please wait for approval'],200);
                    }else{
                        return response()->json(['status'=>false,'message'=>'Something went wrong'],401);

                    }
                }else if (Auth::user()->user_role == 3){
                    $subSubCategory = SubSubCategory::create([
                        'sub_category_id'=>$request->subcategory_id,
                'creator_id'=>Auth::user()->id,
                'name'=>ucfirst($request->name),
                'slug'=>Str::slug($request->slug),
                'is_approved'=>true
           ]);

                    if($subSubCategory){
                        return response()->json(['status'=>true,'message'=>'Successfully added '.ucfirst($request->name)],200);
                    }else{
                        return response()->json(['status'=>false,'message'=>'Something went wrong'],401);

                    }
                }else{
                    return response()->json(['status'=>false,'message'=>'You are not permissible to add sub sub category'],401);

                }
            }else{
                return response()->json(['status'=>false,'message'=>'Change Sub Sub category name'],401);
                
            }
          }
      }
  }else{
      return response()->json(['status'=>false,'message'=>'No sub category found with this id'],401);
      
  }
    }
    
    public function approve($id){
        $subSubCategory = SubSubCategory::where('id',$id)->first();
        if($subSubCategory->is_approved){
            return response()->json(['status'=>false,'message'=>'SubSubCategory already approved!'],401);
            
        }else{
            $status = $subSubCategory->update(['is_approved'=>true]);
            $notification = Notification::create([
                'notification_type_id'=>9,
                'from_id'=>Auth::user()->id,
                'receiver_id'=>$subSubCategory->creator_id,
                'receiver_role_id'=>2,
                'ref_id'=>$subSubCategory->id,
                'tamplate'=>'An admin approved your sub sub category..!Now you can add this sub category to your product',
          ]);
            if($status && $notification){
                return response()->json(['status'=>true,'message'=>'Sub sub Category approved!','data'=>$subSubCategory],200);
                
            }else{
                return response()->json(['status'=>false,'message'=>'Something went wrong!'],401);
                
            }
        }
    }
    public function delete($id){
        $subSubCategory = SubSubCategory::where('id',$id)->first();
        if($subSubCategory){
            if(Auth::user()->user_role == 3){
                if($subSubCategory->is_approved == false){
                    $subSubCategory->delete();
                    return response()->json(['status'=>true,'message'=>'Deleted!'],401);
                    
                }else{
                    if(Product::where('subcategory_id',$id)->first() == null){
                        $subSubCategory->delete();
                        return response()->json(['status'=>true,'message'=>'Deleted!'],401);
                        
                    }else{
                        return response()->json(['status'=>false,'message'=>'You can not delete sub category right now!'],401);
                        
                    }
                    
                }
            }else if (Auth::user()->user_role == 2){
                if($subSubCategory->creator_id == Auth::user()->id){
                    if($subSubCategory->is_approved == false){
                        $subSubCategory->delete();
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

}
