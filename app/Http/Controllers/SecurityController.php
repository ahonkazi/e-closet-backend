<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangeEmailOtpRequest;
use App\Http\Requests\ChangeEmailRequest;
use App\Http\Requests\PasswordChangeRequest;
use App\Mail\ChangeEmailOtpMail;
use App\Models\ChangeEmailOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class SecurityController extends Controller
{
    //
    
    public function changePassword(PasswordChangeRequest $request){
        $user = Auth::user();
        if(Hash::check($request->old_password,$user->password)){
            $updateStatus = $user->update([
        'password'=>Hash::make($request->password)
    ]);
            if($updateStatus){
                return response()->json(['status'=>true,'message'=>'Password changed'],200);
            }else{
                return response()->json(['status'=>false,'message'=>'Something went wrong'],500);
            }
        }else{
            return response()->json(['status'=>false,'message'=>'Password incorrect'],401);
            
        }
    }
    
    public function changeEmailOtp(ChangeEmailOtpRequest $request){
        $otp = random_int(111111,999999);
        $data = ['otp'=>$otp];
        $mailStatus = Mail::to($request->email)->send(new ChangeEmailOtpMail($data));
        if($mailStatus){
            if(ChangeEmailOtp::all()->where('email',$request->email)->first()){
                ChangeEmailOtp::all()->where('email',$request->email)->first()->delete();
                $status =  ChangeEmailOtp::create([
                    'email'=>$request->email,
                'otp'=>$otp
                 ]);

                if($status){
                    return response()->json(['status'=>true,'message'=>'Send otp Success','expire_time'=>150],200);

                }else{
                    return response()->json(['status'=>false,'message'=>'Failed to send otp'],401);
                }
            }else{
                $status =  ChangeEmailOtp::create([
                    'email'=>$request->email,
                'otp'=>$otp
                 ]);

                if($status){
                    return response()->json(['status'=>true,'message'=>'Send otp Success'],200);

                }else{
                    return response()->json(['status'=>false,'message'=>'Failed to send otp'],401);
                }
            }

        }else{
            return response()->json(['status'=>false,'message'=>'Failed to send otp'],401);

        }
    }
    
    public function changeUserEmail(ChangeEmailRequest $request){
        $otpItem = ChangeEmailOtp::where('email',$request->email)->first();
        if($otpItem != null ){
            if($request->otp == $otpItem->otp){
                if(time() - $otpItem->created_at->timestamp < 150){
                    $user = Auth::user();
                    if(Hash::check($request->password,$user->password)){
                            $changeEmailStatus = $user->update(['email'=>$request->email]);
                            if($changeEmailStatus){
                                return response()->json(['status'=>true,'message'=>'Changed'],200);
                                
                            }else{
                                return response()->json(['status'=>false,'message'=>'something went wrong'],500);
                            }
                    }else{
                        //
                    return response()->json(['status'=>false,'message'=>'Password not matched'],401);

                    }
                }else{
                   
                    return response()->json(['status'=>false,'message'=>'Otp expired'],500);

                }
            }else{
                return response()->json(['status'=>false,'message'=>'Otp not matched'],500);

            }
        }
    }
}
