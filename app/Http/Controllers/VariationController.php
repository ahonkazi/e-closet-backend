<?php

namespace App\Http\Controllers;

use App\Http\Requests\VariationStoreRequest;
use App\Models\ProductVariation;
use App\Models\Variation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class VariationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return response()->json(['status'=>true,'data'=>Variation::all()->where('vendor_id',Auth::user()->id)->sortByDesc('id')]) ;
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VariationStoreRequest $request)
    {
        //
        if(Auth::user()->user_role == 2){
            if(Variation::where('name',ucfirst($request->name))->where('vendor_id',Auth::user()->id)->first() == null){
                $variation = Variation::create([
                    'vendor_id'=>Auth::user()->id,
       'name'=>ucfirst($request->name),
       'code'=>strtolower($request->name),
    ]);
                if($variation){
                    return response()->json(['status'=>true,'message'=>'Variation created successfully','data'=>$variation],200);
                }else{
                    return response()->json(['status'=>false,'message'=>'Something went wrong'],401);

                }
            }else{
                return response()->json(['status'=>false,'message'=>'Variation exists'],401);

            }
         
        }
        else{
            return response()->json(['status'=>false,'message'=>'You are not a vendor'],401);

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        //
     if(Auth::user()->user_role == 2){

         $variation = Variation::where('id',$id)->where('vendor_id',Auth::user()->id)->first();
         if($variation){
             if(ProductVariation::where('variation_id',$variation->id)->first() == null){
                 $status = $variation->delete();
                 if($status){
                     return response()->json(['status'=>true,'message'=>'Variation deleted'],200);

                 }else{
                     return response()->json(['status'=>false,'message'=>'Something went wrong'],401);

                 }
             }else{
                 return response()->json(['status'=>false,'message'=>'You can not delete variation coz,So many products has it'],401);
             }
         }else{
             return response()->json(['status'=>false,'message'=>'Variation not found'],401);

         }
     }else{
         return response()->json(['status'=>false,'message'=>'You are not a vendor'],401);
         
     }

    }
}
