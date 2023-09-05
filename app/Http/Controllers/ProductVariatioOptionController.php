<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariationOption;
use App\Models\ProductVariation;
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
        $is_added = false;
        $variation_Types = [];
        if(is_array($datas) && count($datas) == 2 ){
            for($i =0;$i < count($datas);$i++){
                if(($datas[$i]["variation_id"]??null) && ($datas[$i]["product_id"]??null) && ($datas[$i]["values"]??null)){
                    $is_ok = true;
                    if(Product::where('id',$datas[$i]['product_id'])->where('vendor_id',Auth::user()->id)->first()){
                        $variation =Variation::where('id',$datas[$i]['variation_id'])->where('vendor_id',Auth::user()->id)->first();
                        array_push($variation_Types,$variation->is_primary);
                    }
                }else{
                    $is_ok = false;
                }
            }
        }else{
            $is_ok = false;
            
        }
        
        if($is_ok && ($variation_Types[0] != $variation_Types[1])){


            for($i =0;$i < count($datas);$i++){
                if(Product::where('id',$datas[$i]['product_id'])->where('vendor_id',Auth::user()->id)->first()){
                    $variation =Variation::where('id',$datas[$i]['variation_id'])->where('vendor_id',Auth::user()->id)->first();
                    if($variation){
                        $productVariation = ProductVariation::where('variation_id',$datas[$i]['variation_id'])->where('product_id',$datas[$i]['product_id'])->where('vendor_id',Auth::user()->id)->first();
                        if($productVariation == null){
                            if($variation->is_primary){
                                $TrueVariation = ProductVariation::where('is_primary',1)->where('product_id',$datas[$i]['product_id'])->where('vendor_id',Auth::user()->id)->first();
                              if($TrueVariation){
                                  $allProductTVOptions = ProductVariationOption::all()->where('product_variation_id',$TrueVariation->id)->where('vendor_id',Auth::user()->id);
                                    if($allProductTVOptions){
                                          foreach($allProductTVOptions as $option ){
                                              $option->delete();
                                          }
                                    }
                                    $TrueVariation->delete();
                              }

                            }else{
                                $FalseVariation = ProductVariation::where('is_primary',0)->where('product_id',$datas[$i]['product_id'])->where('vendor_id',Auth::user()->id)->first();
                                if($FalseVariation){
                                    $allProductFalseVOptions = ProductVariationOption::all()->where('product_variation_id',$FalseVariation->id)->where('vendor_id',Auth::user()->id);
                                    if($allProductFalseVOptions){
                                        foreach($allProductFalseVOptions as $option ){
                                            $option->delete();
                                        }

                                    }
                                    $FalseVariation->delete();
                                    //
                                }

                            }

                            $protuct_variation = ProductVariation::create([
                               'product_id'=>$datas[$i]['product_id'],
                                'variation_id'=>$datas[$i]['variation_id'],
                                'vendor_id'=>Auth::user()->id,
                                'is_primary'=>$variation->is_primary
                        ]);
                           $values = $datas[$i]['values'];
                           for ($j=0;$j<count($values);$j++){
                               if(ProductVariationOption::where('value',ucfirst($values[$j]))->where('product_id',$datas[$i]['product_id'])->where('vendor_id',Auth::user()->id)->first() == null){
                                   $variationOption =   ProductVariationOption::create([
                                'product_id'=>$datas[$i]['product_id'],
                                'product_variation_id'=>$protuct_variation->id,
                                'vendor_id'=>Auth::user()->id,
                                'value'=>ucfirst($values[$j])
                            ]);
                                   if($variationOption){
                                       $status = true;
                                   }else{
                                       $status = false;
                                   }
                              $is_added = false;
                               }else{
                                   $is_added = true;
                               }
                           }
                       }else{
                            $values = $datas[$i]['values'];
                            for ($j=0;$j<count($values);$j++){
                                if(ProductVariationOption::where('value',ucfirst($values[$j]))->where('product_id',$datas[$i]['product_id'])->where('vendor_id',Auth::user()->id)->first() == null){
                                    $variationOption =   ProductVariationOption::create([
                                        'product_id'=>$datas[$i]['product_id'],
                                'product_variation_id'=>$productVariation->id,
                                'vendor_id'=>Auth::user()->id,
                                'value'=>ucfirst($values[$j])
                            ]);
                                    if($variationOption){
                                        $status = true;
                                    }else{
                                        $status = false;
                                    }
                              $is_added = false;
                                }else{
                                    $is_added = true;
                                }
                            }
                        }

                    }else{
                        return response()->json(['status'=>false,'message'=>'No Variation found'],404);
                    }
                }else{
                    return response()->json(['status'=>false,'message'=>'No product found'],404);
                    
                }           
    
            }
            if($status){
                return response()->json(['status'=>true,'message'=>'Option added'],200);
                
            }else{
                    if($is_added){
                        return response()->json(['status'=>false,'message'=>'Already added!'],404);

                    }else{
                        return response()->json(['status'=>false,'message'=>'Something went wrong'],404);

                    }
            }
        }else{
            return response()->json(['status'=>false,'message'=>'Please send valid data'],404);
            
        }
        
  
    }

//        public function primary(){
//        return response()->json(['status'=>true,'data'=>Variation::select('id','name')->where('vendor_id',Auth::user()->id)->where('is_primary',1)->get()],200);
//    }
//       public function secondary(){
//        return response()->json(['status'=>true,'data'=>Variation::select('id','name')->where('vendor_id',Auth::user()->id)->where('is_primary',0)->get()],200);
//    }

      public function primary($id){
        $product = Product::where('id',$id)->where('vendor_id',Auth::user()->id)->first();
        if($product){
            $options = ProductVariationOption::where('vendor_id',Auth::user()->id)->where('product_id',$product->id);
            return "ok";

        }
    }
}
