<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCategoryRequest;
use App\Models\Category;
use App\Models\Notification;
use App\Models\SubCategory;
use App\Models\SubSubCategory;
use App\Models\UserDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
class CategoryController extends Controller
{
    //
    
    public function add(AddCategoryRequest $request){
        if(Category::where('name',ucfirst($request->name))->first()){
            return response()->json(['status'=>false,'message'=>$request->name.' Already exists'],401);
        }else{
            if(Category::where('slug',Str::slug($request->slug))->first()){
                return response()->json(['status'=>false,'message'=>$request->slug.' Already exists'],401);
            }else{
                if(SubCategory::where('name',ucfirst($request->name))->first() == null ){
                    if(Auth::user()->user_role == 2){
                        $category = Category::create([
                            'creator_id'=>Auth::user()->id,
                'name'=>ucfirst($request->name),
                'slug'=>Str::slug($request->slug),
                'icon_id'=>$request->icon_id,
           ]);
                        $ganderPerson = Str::lower(UserDetails::where('user_id',Auth::user()->id)->first()->gander) == 'male'?'his':'her';
                        $notification = Notification::create([
                            'notification_type_id'=>4,
                              'from_id'=>Auth::user()->id,
                              'receiver_id'=>null,
                              'receiver_role_id'=>3,
                              'ref_id'=>$category->id,
                              'tamplate'=>Auth::user()->firstName.' Created a category.Review '.$ganderPerson.' category and approve',
                            ]);
                        if($category && $notification){
                            return response()->json(['status'=>true,'message'=>'Successfully added '.ucfirst($request->name).'. Please wait for approval'],200);
                        }else{
                            return response()->json(['status'=>false,'message'=>'Something went wrong'],401);

                        }
                    }else if (Auth::user()->user_role == 3){
                        $category = Category::create([
                            'creator_id'=>Auth::user()->id,
                'name'=>ucfirst($request->name),
                'slug'=>Str::slug($request->slug),
                'is_approved'=>true
           ]);

                        if($category){
                            return response()->json(['status'=>true,'message'=>'Successfully added '.ucfirst($request->name)],200);
                        }else{
                            return response()->json(['status'=>false,'message'=>'Something went wrong'],401);

                        }
                    }else{
                        return response()->json(['status'=>false,'message'=>'You are not permissible to add category'],401);

                    }
                }else{
                    return response()->json(['status'=>false,'message'=>'Change category name'],401);

                }
            }
        }
    }
    
    public function approve($id){
        $category = Category::where('id',$id)->first();
        if($category->is_approved){
            return response()->json(['status'=>false,'message'=>'Category already approved!'],401);
            
        }else{
            $status = $category->update(['is_approved'=>true]);
            $notification = Notification::create([
                'notification_type_id'=>5,
                'from_id'=>Auth::user()->id,
                'receiver_id'=>$category->creator_id,
                'receiver_role_id'=>2,
                'ref_id'=>$category->id,
                'tamplate'=>'An admin approved your category..!Now you can add this category to your product',
          ]);
            if($status && $notification){
                return response()->json(['status'=>true,'message'=>'Category approved!','data'=>$category],200);
                
            }else{
                return response()->json(['status'=>false,'message'=>'Something went wrong!'],401);
                
            }
        }
    }
        public function delete($id){
        $category = Category::where('id',$id)->first();
        if($category){
            if(Auth::user()->user_role == 3){
                if($category->is_approved == false){
                    $category->delete();
                    return response()->json(['status'=>true,'message'=>'Deleted!'],401);

                }else{
                    return response()->json(['status'=>false,'message'=>'You can not delete category right now!'],401);

                }
            }else if (Auth::user()->user_role == 2){
                if($category->creator_id == Auth::user()->id){
                    if($category->is_approved == false){
                        $category->delete();
                    }else{
                        return response()->json(['status'=>false,'message'=>'You can not delete category right now!'],401);

                    }
                }else{
                    return response()->json(['status'=>false,'message'=>'You are not permissible to delete category'],401);

                }
            }else{
                return response()->json(['status'=>false,'message'=>'You are not permissible to delete category'],401);

            }
        }else{
            return response()->json(['status'=>false,'message'=>'No category found with this id!'],401);

        }
    }
    public function categories(){
        return response()->json(['status'=>true,'data'=>Category::with('sub_categories.subSubCategory','icon')->get()->where('is_approved',true)],200);
    }

    public function allCategories(){
        return response()->json(['status'=>true,'data'=>Category::with('sub_categories.subSubCategory','icon')->get()],200);

    }
}
