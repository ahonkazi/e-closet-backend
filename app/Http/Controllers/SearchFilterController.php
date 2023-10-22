<?php

namespace App\Http\Controllers;

use App\Models\DisplayVariation;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Variation;
use Illuminate\Http\Request;

class SearchFilterController extends Controller
{
    //
    public function getFilters(Request $request){
        $query = Product::query();
        if($request->has('q')){
            $query->where('title','like','%'.$request->q.'%')->orWhere('discription','like','%'.$request->q.'%');
        }
        $productIds = [];
        foreach ($query->get() as $product){
            array_push($productIds,$product->id);
        }
        $productVariationIds = [];
        foreach (ProductVariation::all()->whereIn('product_id',$productIds) as $productVariation){
            array_push($productVariationIds,$productVariation->variation_id);
        }
        $displayVariationIds = [];
        foreach (Variation::all()->whereIn('id',$productVariationIds) as $variation){
            array_push($displayVariationIds,$variation->display_variation_id);
        }
        $displayVariations = DisplayVariation::with('options')->whereIn('id',$displayVariationIds)->get();
        return $displayVariations;
    }
}
