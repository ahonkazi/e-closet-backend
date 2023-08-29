<?php

namespace App\Http\Controllers;
use App\Mail\vendorApprovalMail;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ApprovalController extends Controller
{
    //
    public function VendorApproval(Request $request,$vendor_id){
        try {
            $vendor = User::where('id',$vendor_id)->first();
            if($vendor){
                if($vendor->user_role == 2){
                    if($vendor->is_approved == false){
                        $status = $vendor->update(['is_approved'=>1]);
                        if($status){
                            $user = Auth::user();
                            $notification = Notification::create([
                                'notification_type_id'=>3,
                              'from_id'=>$user->id,
                              'receiver_id'=>$vendor_id,
                              'receiver_role_id'=>2,
                              'ref_id'=>$vendor_id,
                              'tamplate'=>'You are approved by Admin.Now you can add product and so on.',
                        ]);
                            $data = ['text'=>'You are approved by Admin.Now you can add product and so on.'];
                            $mailStatus = Mail::to(User::where('id',$vendor_id)->first()->email)->send(new vendorApprovalMail($data));
                            if($notification && $mailStatus){
                                return response()->json(['status'=>true,'message'=>'Vendor Approved'],200);

                            }else{
                                return response()->json(['status'=>false,'message'=>'Opps problem with approved vendor,try again later'],401);

                            }

                        }else{
                            return response()->json(['status'=>false,'message'=>'Opps problem with approved vendor,try again later'],401);

                        }
                    }else{
                        return response()->json(['status'=>false,'message'=>'Vendor already approved'],401);

                    }
        
                }else{
                    return response()->json(['status'=>false,'message'=>'User is not a vendor'],401);
                }
            }else{
                return response()->json(['status'=>false,'message'=>'No vendor found with this id'],401);
            }
        }catch (\Exception $exception){
            return response()->json(['code'=>$exception->getCode(),'message'=>$exception->getMessage()]);
        }
    

    }
}
