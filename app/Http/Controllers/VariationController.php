<?php

namespace App\Http\Controllers;

use App\Http\Requests\VariationStorRequest;
use App\Models\Variation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VariationController extends Controller
{
    //

    public function store(VariationStorRequest $request){
        if(Variation::where('name',ucfirst($request->name))->where('vendor_id',Auth::user()->id)->first() == null){
                $variation = Variation::create([
        'vendor_id' => Auth::user()->id,
        'name'=>ucfirst($request->name),
        'is_primary'=>$request->is_primary
    ]);
                if($variation){
                    return response()->json(['status'=>true,'message'=>$request->name.' added','data'=>$variation],200);

                }else{
                    return response()->json(['status'=>false,'message'=>'Something went wrong'],401);

                }
        }else{
            return response()->json(['status'=>false,'message'=>$request->name.' already exists'],401);
        }
    }
    public function delete($id){
        $variation = Variation::where('id',$id)->where('vendor_id',Auth::user()->id)->first();
        if($variation){
            $status = $variation->delete();
            if($status){
                return response()->json(['status'=>true,'message'=>'Variation deleted'],200);

            }else{
                return response()->json(['status'=>false,'message'=>'Something went wrong'],401);

            }
        }else{
            return response()->json(['status'=>false,'message'=>'Not Found'],404);

        }
    }
        public function all(){
        return response()->json(['status'=>true,'data'=>Variation::select('id','name')->where('vendor_id',Auth::user()->id)->get()],200);
    }
    public function primary(){
        return response()->json(['status'=>true,'data'=>Variation::select('id','name')->where('vendor_id',Auth::user()->id)->where('is_primary',1)->get()],200);
    }
       public function secondary(){
        return response()->json(['status'=>true,'data'=>Variation::select('id','name')->where('vendor_id',Auth::user()->id)->where('is_primary',0)->get()],200);
    }
}
