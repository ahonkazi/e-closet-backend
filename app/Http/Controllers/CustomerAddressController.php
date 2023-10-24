<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerAddressCreateRequest;
use App\Models\AddressType;
use App\Models\CustomerAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerAddressController extends Controller
{
    //
   
public function create(CustomerAddressCreateRequest $request)
    {
        $user_id = Auth::user()->id;
        $type_list = [1, 2, 3];
        if (in_array($request->address_type_no, $type_list)) {
            $address = CustomerAddress::where('customer_id', $user_id)->where('address_type_no', $request->address_type_no)->first();
            if ($address) {
                return response()->json(['status'=>true,'message'=>'Already exists!'],500);
            } else {
                $newAddress = CustomerAddress::create([
                    'customer_id' => $user_id,
                    'country' => $request->country,
                    'district' => $request->district,
                    'sub_district' => $request->sub_district,
                    'street_address' => $request->street_address,
                    'appartment_number' => $request->appartment_number,
                    'postal_code' => $request->postal_code,
                    'phone' => $request->phone,
                    'address_type_no' => $request->address_type_no,
                ]);
                if($newAddress){
                    return response()->json(['status'=>true,'data'=>$newAddress],200);
                }else{
                    return response()->json(['status'=>true,'message'=>'Something went wrong'],500);
                }
            }
        } else {
            return response()->json(['status'=>true,'message'=>'Incorrect type no'],500);
        }
    }
    public function delete($address_id){
    $user_id = Auth::user()->id;
    $address = CustomerAddress::where('id',$address_id)->where('customer_id',$user_id)->first();
    if($address){
        $status = $address->delete();
        if($status){
            return response()->json(['status'=>true,'message'=>'Deleted'],200);
        }else{
            return response()->json(['status'=>false,'message'=>'Something went wrong'],500);
            
        }
    }else{
        return response()->json(['status'=>false,'message'=>'Not found'],404);
        
    }
}
}
