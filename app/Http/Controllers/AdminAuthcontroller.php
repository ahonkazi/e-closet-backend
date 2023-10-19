<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterOtpRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegistrationRequest;
use App\Mail\LoginOtpMail;
use App\Mail\RegisterOtpMail;
use App\Models\Login;
use App\Models\LoginOtp;
use App\Models\Notification;
use App\Models\RegisterOtp;
use App\Models\User;
use App\Models\UserDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Passport\RefreshToken;
use Laravel\Passport\Token;
class AdminAuthcontroller extends Controller
{
    // 
     public function sendOtp(RegisterOtpRequest $request){
         $otp = random_int(111111,999999);
         $data = ['otp'=>$otp];
         $mailStatus = Mail::to($request->email)->send(new RegisterOtpMail($data));
         if($mailStatus){
             if(RegisterOtp::all()->where('email',$request->email)->first()){
                 RegisterOtp::all()->where('email',$request->email)->first()->delete();

                 $status =  RegisterOtp::create([
                     'email'=>$request->email,
                'otp'=>$otp
                 ]);

                 if($status){
                     return response()->json(['status'=>true,'message'=>'Send otp Success','expire_time'=>150],200);

                 }else{
                     return response()->json(['status'=>false,'message'=>'Failed to send otp'],401);
                 }
             }else{
                $status =  RegisterOtp::create([
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
    public function register(UserRegistrationRequest $request){
         $otpItem = RegisterOtp::where('email',$request->email)->first();
         if($otpItem != null ){
            if($request->otp == $otpItem->otp){
                if(time() - $otpItem->created_at->timestamp < 150){
                    $image = $request->file('profile_pic');
                    $fileName = 'ecloset_img'.random_int(1111,9999).time().'.'.$image->getClientOriginalExtension();
                    $uploadStatus = $image->storeAs('images',$fileName,'public');
                    if($uploadStatus){
                        $user = User::create([
                            'firstname'=>$request->firstname,
                            'lastname'=>$request->lastname,
                            'email'=>$request->email,
                            'password'=>Hash::make($request->password),
                            'profile_pic'=>'/storage/images/'.$fileName,
                            'user_role'=>3,
                            'unique_id'=>random_int(11111,99999).time(),

                        ]);
                        $userAgent = new \Jenssegers\Agent\Agent();
                        $loginDetails = Login::create([
                            'ip'=>$request->ip(),
                            'browser'=>$userAgent->browser(),
                            'platform'=>$userAgent->platform(),
                            'version'=>$userAgent->version($userAgent->platform()),
                            'user_id'=>$user->id,
                    ]);
                        if($user){

                          $userdetails =   UserDetails::create([
                            'user_id'=>$user->id,
                            'country'=>$request->country,
                            'city'=>$request->city,
                            'profession'=>$request->profession,
                            'date_of_birth'=>$request->date_of_birth,
                            'gander'=>$request->gander,
                         ]);
                  
                          $ganderPerson = Str::lower(UserDetails::where('user_id',$user->id)->first()->gander) == 'male'?'his':'her';
                          $notification = Notification::create([
                              'notification_type_id'=>2,
                              'from_id'=>$user->id,
                              'receiver_id'=>null,
                              'receiver_role_id'=>3,
                              'ref_id'=>$user->id,
                              'tamplate'=>$user->firstname.' Created an account as an admin.Review '.$ganderPerson.' details and approve',
                            ]);
            
                          
                          if($userdetails && $notification){
                                $token = $user->createToken('accessToken')->accessToken;
                                $data = ['token'=>$token,'user'=>$user];
                                return response()->json(['status'=>true,'message'=>'Registration Successfull','data'=>$data],401);
                                
                            }else{
                                return response()->json(['status'=>false,'message'=>'Registration Failed'],401);
                                
                            }
                           
                        }else{
                            return response()->json(['status'=>false,'message'=>'Registration Failed'],401);
                            
                        }
                    }else{
                        return response()->json(['status'=>false,'message'=>'Problem With Uploading profile pic'],401);
                        
                    }

                }else{
                    return response()->json(['status'=>false,'message'=>'Otp Expired'],401);
                    
                }
                
            }else{
                return response()->json(['status'=>false,'message'=>'Invalid Otp Key'],401);
                
            }
        }else{
             return response()->json(['status'=>false,'message'=>'No  Otp Key Found With this email'],401);
         }
     }

   public function sendLoginOtp(LoginOtpRequest $request){
         $user = User::where('email',$request->email)->first();
         if($user){
             if(Hash::check($request->password,$user->password)){
                 if($user->user_role == 3){
                     $otp = random_int(111111,999999);
                     $data = ['otp'=>$otp];
                     $mailStatus = Mail::to($request->email)->send(new LoginOtpMail($data));
                     if($mailStatus){
                         if(LoginOtp::all()->where('email',$request->email)->first()){
                             LoginOtp::all()->where('email',$request->email)->first()->delete();

                             $status =  LoginOtp::create([
                                 'email'=>$request->email,
                'otp'=>$otp
                 ]);

                             if($status){
                                 return response()->json(['status'=>true,'message'=>'Send otp Success','expire_time'=>150],200);

                             }else{
                                 return response()->json(['status'=>false,'message'=>'Failed to send otp'],401);
                             }
                         }else{
                             $status =  LoginOtp::create([
                                 'email'=>$request->email,
                'otp'=>$otp
                 ]);

                             if($status){
                                 return response()->json(['status'=>true,'message'=>'Send otp Success','expire_time'=>150],200);

                             }else{
                                 return response()->json(['status'=>false,'message'=>'Failed to send otp'],401);
                             }
                         }

                     }else{
                         return response()->json(['status'=>false,'message'=>'Failed to send otp'],401);

                     }
                 }else{
                     return response()->json(['status'=>false,'message'=>'Invalid email or password'],401);
                 }
             }else{
                 return response()->json(['status'=>false,'message'=>'Invalid email or password'],401);

             }
         }else{
             return response()->json(['status'=>false,'message'=>'No account found'],401);
         }

     }
    public function login(UserLoginRequest $request){
         $user = User::where('email',$request->email)->first();
         if($user){
             if(Hash::check($request->password,$user->password)){
                 if($user->user_role == 3){
                     if($user->two_step_verification){
                         $otpItem = LoginOtp::where('email',$request->email)->first();
                         if($otpItem != null ){
                             if($request->otp == $otpItem->otp){
                                 if(time() - $otpItem->created_at->timestamp < 150){
                                     $token = $user->createToken('accessToken')->accessToken;
                                     $data = ['token'=>$token,'user'=>$user];
                                     $userAgent = new \Jenssegers\Agent\Agent();
                                     $loginDetails = Login::create([
                                         'ip'=>$request->ip(),
                            'browser'=>$userAgent->browser(),
                            'platform'=>$userAgent->platform(),
                            'version'=>$userAgent->version($userAgent->platform()),
                            'user_id'=>$user->id,
                    ]);
                                     return response()->json(['status'=>true,'message'=>'Logged in Successfull','data'=>$data],200);
                                 }else{
                                     return response()->json(['status'=>false,'message'=>'Otp Expired'],401);

                                 }
                             }else{
                                 return response()->json(['status'=>false,'message'=>'Invalid Otp Key'],401);

                             }

                         }else{
                             return response()->json(['status'=>false,'message'=>'No Otp Key Found With this email'],401);

                         }


                     }else{
                         $token = $user->createToken('accessToken')->accessToken;
                         $data = ['token'=>$token,'user'=>$user];
                         $userAgent = new \Jenssegers\Agent\Agent();
                         $loginDetails = Login::create([
                             'ip'=>$request->ip(),
                            'browser'=>$userAgent->browser(),
                            'platform'=>$userAgent->platform(),
                            'version'=>$userAgent->version($userAgent->platform()),
                            'user_id'=>$user->id,
                    ]);
                         return response()->json(['status'=>true,'message'=>'Logged in Successfull','data'=>$data],200);
                     }  
                 }else{
                     return response()->json(['status'=>false,'message'=>'No Account Found'],401);
                     
                 }
     
             }else{
                 return response()->json(['status'=>false,'message'=>'Incorrect password'],401);

             }
         }else{
             return response()->json(['status'=>false,'message'=>'No Account Found'],401);
         }
     }

     public function logout(){
         $user = Auth::user()->token();
         $user->revoke();
         return response()->json(['status'=>true,'message'=>'Logut successfull']);
     }

     public function data(){
         return 'ok';
     }
}
