<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductReviewAddRequest;
use App\Http\Requests\ProductReviewEditRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductReviewController extends Controller
{
    //

    public function add(ProductReviewAddRequest $request, $product_id)
    {
        $product = Product::where('id', $product_id)->first();
        $user_id = Auth::user()->id;
        if ($product) {
            $OrderItem = Order::where('id', OrderItem::where('product_id', $product_id)->where('customer_id', $user_id)->first()->order_id)->where('order_status', 'Delivered')->first();
            if ($OrderItem) {
                $ratingLists = [1, 2, 3, 4, 5];
                if (!in_array($request->rating, $ratingLists)) {
                    return response()->json(['status' => false, 'message' => 'Rating must be in 1 to 5'], 500);
                }
                $oldReviews = ProductReview::all()->where('product_id', $product_id)->where('customer_id', $user_id)->first();
                if ($oldReviews) {
                    return response()->json(['status' => false, 'message' => 'You already added a Review to this product'], 500);

                }

                $review = ProductReview::create([
                    'customer_id' => $user_id,
                    'product_id' => $product_id,
                    'rating' => $request->rating,
                    'message' => $request->message,
                    'recomended' => $request->recomended
                ]);
                if ($review) {
                    return response()->json(['status' => true, 'message' => 'Review added', 'data' => $review], 200);

                } else {
                    return response()->json(['status' => false, 'message' => 'Something went wrong'], 500);

                }
            } else {
                return response()->json(['status' => false, 'message' => 'You can not add review unless you place order'], 403);

            }
        } else {
            return response()->json(['status' => false, 'message' => 'Product not found'], 404);
        }
    }

    public function edit(ProductReviewEditRequest $request, $review_id)
    {
        $user_id = Auth::user()->id;
            $review = ProductReview::where('id', $review_id)->where('customer_id', $user_id)->first();
            if ($review) {
                $ratingLists = [1, 2, 3, 4, 5];
                if (!in_array($request->rating, $ratingLists)) {
                    return response()->json(['status' => false, 'message' => 'Rating must be in 1 to 5'], 500);
                }
                $updatedReview = $review->update([
                    'rating' => $request->rating,
                    'recomended' => $request->recomended,
                    'message' => $request->message
                ]);
                if($updatedReview){
                    return response()->json(['status' => true, 'message' => 'Review updated', 'data' => $review], 200);
                    
                }else{
                    return response()->json(['status' => false, 'message' => 'Unable to edit review.Please try again'], 500);
                    
                }

            } else {
                return response()->json(['status' => false, 'message' => 'Review not found'], 404);

            }
         
    }
    
    public function delete($review_id){
        $user_id = Auth::user()->id;
        $review = ProductReview::where('id', $review_id)->where('customer_id', $user_id)->first();
        if($review){
            $deleteStatus = $review->delete();
            if($deleteStatus){
                return response()->json(['status' => true, 'message' => 'Deleted'], 200);
                
            }else{
                return response()->json(['status' => false, 'message' => 'Something went wrong'], 500);
                
            }
        }else{
            return response()->json(['status' => false, 'message' => 'Review not found'], 404);
            
        }
        
    }
    public function myReviews(){
        $data = ProductReview::all()->where('customer_id',Auth::user()->id)->sortBy("DESC");
        return response()->json(['status'=>true,'data'=>$data],200);
    }
}

