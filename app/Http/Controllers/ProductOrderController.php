<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductOrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ProductVariation;
use App\Models\ProductVariationOption;
use App\Models\ShippingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function PHPUnit\Framework\isEmpty;

class ProductOrderController extends Controller
{
    //


    public function order(ProductOrderRequest $request)
    {

        $order_items = [];
        $errors = [];
        $orderProducts = [];
        $totalPrice = 0;
//        validate products start
        if ($request->has('products')) {
            if (is_array($request->products)) {
                if ($request->products) {
                    $order_items = $request->products;
                } else {
                    array_push($errors, ['message' => 'Select minimum 1 product to place order']);

                }
            }

        } else {
            array_push($errors, ['message' => 'Products required']);

        }
        //        validate products end

        //        validate shipping address start

        $shippingAddress = [];
        if ($request->has('shipping')) {
            $shippingAddress = $request->shipping;
            if (!isset($shippingAddress['first_name'])) {
                array_push($errors, ['first_name' => 'first_name required']);
            }
            if (!isset($shippingAddress['last_name'])) {
                array_push($errors, ['last_name' => 'last_name required']);
            }
            if (!isset($shippingAddress['country'])) {
                array_push($errors, ['country' => 'country required']);
            }
            if (!isset($shippingAddress['district'])) {
                array_push($errors, ['district' => 'district required']);
            }
            if (!isset($shippingAddress['sub_district'])) {
                array_push($errors, ['sub_district' => 'sub_district required']);
            }
            if (!isset($shippingAddress['street_address'])) {
                array_push($errors, ['street_address' => 'street_address required']);
            }
            if (!isset($shippingAddress['appartment_number'])) {
                array_push($errors, ['appartment_number' => 'appartment_number required']);
            }
            if (!isset($shippingAddress['postal_code'])) {
                array_push($errors, ['postal_code' => 'postal_code required']);
            }
            if (!isset($shippingAddress['phone'])) {
                array_push($errors, ['phone' => 'phone required']);
            }

        } else {
            array_push($errors, ['message' => 'Shipping address required']);

        }
        //        validate shipping address end

        //        validate payment_method start

        if ($request->has('payment_method')) {
            if ($request->payment_method != 1) {
                array_push($errors, ['payment_method' => 'Payment method invalid.please select 1 for cash on delevery']);
            }
        } else {
            array_push($errors, ['payment_method' => 'Payment method required']);

        }
        //        validate payment_method start

        if ($errors) {
            return response()->json($errors, 400);
        }
//get total price and validate products array
        foreach ($order_items as $item) {
            if (isset($item['product_id'])) {
                $product = Product::where('id', $item['product_id'])->first();
                if ($product) {
                    if (in_array($product->id, $orderProducts)) {
                        array_push($errors, ['message' => 'Duplicate products found']);
                        return response()->json($errors, 400);
                    } else {
                        array_push($orderProducts, $product->id);
                    }
                    if (isset($item['primary_option_id']) && isset($item['secondary_option_id'])) {
                        $productStock = ProductStock::where('product_id', $item['product_id'])->where('primary_option_id', $item['primary_option_id'])->where('secondary_option_id', $item['secondary_option_id'])->first();
                        if ($productStock) {
                            if (isset($item['quantity'])) {
                                if ($productStock->stock >= (integer)$item['quantity']) {
                                    //go ahead
                                    $totalPrice = $totalPrice + ($productStock->price * (integer)$item['quantity']);
                                } else {
                                    array_push($errors, ['message' => 'Stock limited please order less than ' . $productStock->stock . ' products']);

                                }
                            } else {
                                array_push($errors, ['message' => 'Quantity required for ' . $item['product_id']]);

                            }
                        } else {
                            array_push($errors, ['message' => 'Invalid product variation for ' . $item['product_id']]);

                        }
                    } elseif (isset($item['primary_option_id'])) {
                        $productStock = ProductStock::where('product_id', $item['product_id'])->where('primary_option_id', $item['primary_option_id'])->first();
                        if ($productStock) {
                            if ($productStock->secondary_option_id) {
                                array_push($errors, ['message' => 'Select a secondary variation for ' . $item['product_id']]);
                            } else {
                                if (isset($item['quantity'])) {
                                    if ($productStock->stock >= (double)$item['quantity']) {
                                        $totalPrice = $totalPrice + ($productStock->price * (integer)$item['quantity']);
                                    } else {
                                        array_push($errors, ['message' => 'Stock limited please order less than ' . $productStock->stock . ' products']);

                                    }
                                } else {
                                    array_push($errors, ['message' => 'Quantity required for ' . $item['product_id']]);

                                }
                            }
                        } else {
                            array_push($errors, ['message' => 'Invalid product variation for ' . $item['product_id']]);

                        }


                    } else {
                        array_push($errors, ['message' => 'Please select a product variation for ' . $item['product_id']]);

                    }
                } else {
                    array_push($errors, ['message' => 'Product Not found with ' . $item['product_id']]);
                }
            } else {
                array_push($errors, ['message' => 'product_id required']);

            }


        }


        if ($errors) {
            return response()->json($errors, 400);
        } else {
            //if no errors go proceed shipping

            $user_id = Auth::user()->id;
            $shipping = ShippingAddress::create([
                'customer_id' => $user_id,
                'first_name' => $shippingAddress['first_name'],
                'last_name' => $shippingAddress['last_name'],
                'country' => $shippingAddress['country'],
                'district' => $shippingAddress['district'],
                'sub_district' => $shippingAddress['sub_district'],
                'street_address' => $shippingAddress['street_address'],
                'appartment_number' => $shippingAddress['appartment_number'],
                'postal_code' => $shippingAddress['postal_code'],
                'phone' => $shippingAddress['phone']
            ]);
            if ($shipping) {
                $order = Order::create([
                    'unique_id' => random_int(111111, 999999),
                    'customer_id' => $user_id,
                    'shipping_id' => $shipping->id,
                    'payment_method' => $request->payment_method,
                    'total_price' => $totalPrice,
                    'order_status' => 'Pending'
                ]);
                if ($order) {
                    foreach ($order_items as $item) {
                        $combination = "";
                        if (isset($item['primary_option_id']) && isset($item['secondary_option_id'])) {
                            $PrimaryOption = ProductVariationOption::where('id', $item['primary_option_id'])->where('is_primary', 1)->first();
                            $SecondaryOption = ProductVariationOption::where('id', $item['secondary_option_id'])->where('is_primary', 0)->first();
                            if ($PrimaryOption && $SecondaryOption) {
                                $combination = $PrimaryOption->value . ' + ' . $SecondaryOption->value;
                            }
                        } else if (isset($item['primary_option_id'])) {
                            $PrimaryOption = ProductVariationOption::where('id', $item['primary_option_id'])->where('is_primary', 1)->first();
                            if ($PrimaryOption) {
                                $combination = $PrimaryOption->value;
                            }
                        }
                        $order_item = OrderItem::create([
                            'customer_id' => $user_id,
                            'order_id' => $order->id,
                            'product_id' => $item['product_id'],
                            'combination' => $combination,
                            'quantity' => $item['quantity']
                        ]);
                        if (!$order_item) {
                            $order->delete();
                            $shipping->delete();
                            return response()->json(['status' => false, 'message' => 'Something went wrong'], 500);

                        }
                    }

                    return response()->json(['status' => true, 'message' => 'Order confirmed', 'data' => $order], 200);

                } else {
                    $shipping->delete();
                    return response()->json(['status' => false, 'message' => 'Something went wrong'], 500);

                }
            } else {
                return response()->json(['status' => false, 'message' => 'Something went wrong'], 500);
            }

        }

    }

    public function removeOrder($order_id)
    {
        $user_id = Auth::user()->id;
        $order = Order::where('id', $order_id)->where('customer_id', $user_id)->first();
        if ($order) {
            if ($order->order_status == 'Pending') {
                
                $shipping = ShippingAddress::where('id', $order->shipping_id)->first();
                $orderProducts = OrderItem::all()->where('order_id', $order->id);
                foreach ($orderProducts as $item) {
                    $item->delete();
                }
                $orderDeleteStatus = $order->delete(); 
                if ($orderDeleteStatus) {
                    $shippingDeleteStatus = $shipping->delete();
                    if($shippingDeleteStatus){
                        return response()->json(['status' => true, 'message' => 'Order removed'], 200);
                        
                    }else{
                        return response()->json(['status' => false, 'message' => 'Something went wrong'], 500);
                        
                    }

                } else {
                    return response()->json(['status' => false, 'message' => 'Something went wrong'], 500);

                }
            } else {
                return response()->json(['status' => false, 'message' => 'Order is in processing or completed.You can not remove now!'], 403);

            }
        } else {
            return response()->json(['status' => false, 'message' => 'No order found with this id'], 404);
        }
    }
    
    public function myOrders(){
        $data = Order::all()->where('customer_id',Auth::user()->id);
        return response()->json(['status'=>true,'data'=>$data],200);
    }
}
