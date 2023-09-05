<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariationOption;
use App\Models\Variation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductVariatioOptionController extends Controller
{
    //
    
    public function store(Request $request){
        $datas = $request->all();
        $is_ok = false;
        $status = false;
        if(is_array($datas) && count($datas) == 2 ){
            for($i =0;$i < count($datas);$i++){
                if(($datas[$i]["variation_id"]??null) && ($datas[$i]["product_id"]??null) && ($datas[$i]["values"]??null)){
                        $is_ok = true;
                }else{
                    $is_ok = false;
                }
            }
        }else{
            $is_ok = false;
            
        }
        
        if($is_ok){
            for($i =0;$i < count($datas);$i++){
                if(Product::where('id',$datas[$i]['product_id'])->where('vendor_id',Auth::user()->id)->first()){
                    if(Variation::where('id',$datas[$i]['variation_id'])->where('vendor_id',Auth::user()->id)->first()){
                        $values = $datas[$i]['values'];
                        for ($j=0;$j<count($values);$j++){
                            if(ProductVariationOption::where('value',ucfirst($values[$j]))->where('product_id',$datas[$i]['product_id'])->where('vendor_id',Auth::user()->id)->first() == null){
                            $variationOption =   ProductVariationOption::create([
                                'product_id'=>$datas[$i]['product_id'],
                                'variation_id'=>$datas[$i]['variation_id'],
                                'vendor_id'=>Auth::user()->id,
                                'value'=>ucfirst($values[$j])
                            ]);
                              if($variationOption){
                                  $status = true;
                              }else{
                                  $status = false;
                              }
                          }
                        }
                    }else{
                        return response()->json(['status'=>false,'No Variation found'],404);
                    }
                }else{
                    return response()->json(['status'=>false,'No product found'],404);
                    
                }           
    
            }
            if($status){
                return response()->json(['status'=>true,'Option added'],200);
                
            }else{
                return response()->json(['status'=>false,'Something went wrong.Please send valid data'],404);
                
            }
        }else{
            return response()->json(['status'=>false,'Something went wrong.Please send valid data'],404);
            
        }
        
  
    }
}
