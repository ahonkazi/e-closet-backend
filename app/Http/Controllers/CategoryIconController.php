<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryIconAddRequest;
use App\Models\Category;
use App\Models\CategoryIcon;
use Illuminate\Http\Request;

class CategoryIconController extends Controller
{
    //
    
    public function add(CategoryIconAddRequest $request){
        $getSvg = CategoryIcon::where('svg_code',$request->svg_code)->first();
    if($getSvg){
        return response()->json(['status'=>false,'message'=>'already exists'],200);
        
    }else{
        $status = CategoryIcon::create([
            'svg_code'=>$request->svg_code,
    ]);
        if($status){
            return response()->json(['status'=>true,'data'=>$status,'message'=>'added'],200);
        }
    }
    }
    
      public function delete(Request $request,$id){
      $icon = CategoryIcon::where('id',$id)->first();
      $category = Category::where('icon_id',$id)->first();
      if($category){
          return response()->json(['status'=>false,'message'=>'Can not delete!'],200);
      }else{
          $icon->delete();
      }
       
    }
}
