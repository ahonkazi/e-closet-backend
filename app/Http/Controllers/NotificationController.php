<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\NotificationType;
use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    //
    
    public function notifications(){
        if(Auth::user()->user_role == 3){
            $notificationList = Notification::with('notificationType')->get()->where('receiver_role_id',3)->sortByDesc('id');
            return response()->json($notificationList);
        }else{
            $notificationList = Notification::with('notificationType')->get()->where('receiver_id',Auth::user()->id)->sortByDesc('id');
            return response()->json($notificationList);
            
        }
    }
    public function readNotification($id){
        if(Auth::user()){
            
            $notification = Notification::where('receiver_id',Auth::user()->id)->where('id',$id)->first();
            if($notification){
               if($notification->read_status == false){
                   $status = $notification->update(['read_status'=>true]);
                   if($status){
                       return response()->json(['status'=>true,'message'=>'Notification read successfully.','data'=>$notification],200);

                   }
                else{
                    return response()->json(['status'=>false,'message'=>'Something went wrong,unable to read.'],401);

                }
               }else{
               
                   return response()->json(['status'=>true,'message'=>'Notification already read.','data'=>$notification],200);
                                          
                   
               }
            }else{
                return response()->json(['status'=>false,'message'=>'Something went wrong'],401);
                
            }
        }else{
            return response()->json(['status'=>false,'message'=>'You are not logged in'],401);
        }
    }
        public function unReadNotification($id){
        if(Auth::user()){

            $notification = Notification::where('receiver_id',Auth::user()->id)->where('id',$id)->first();
            if($notification){
           if($notification->read_status){
               $status = $notification->update(['read_status'=>false]);
               if($status){
                   return response()->json(['status'=>true,'message'=>'Notification unread successfully.','data'=>$notification],200);

               }else{
                   return response()->json(['status'=>false,'message'=>'Something went wrong,unable to read.'],401);

               }
           }else{
               return response()->json(['status'=>true,'message'=>'Notification already unread.','data'=>$notification],200);
               
           }
            }else{
                return response()->json(['status'=>false,'message'=>'Something went wrong'],401);

            }
        }else{
            return response()->json(['status'=>false,'message'=>'You are not logged in'],401);
        }
    }

    public function delete($id){

    if(Notification::where('id',$id)->first()){
        if(Auth::user()->user_role == 3){
            $notification = Notification::where('id',$id)->where('receiver_role_id',3)->first();
            if($notification){
              $notification->delete();
              return response()->json(['status'=>true,'message'=>'deleted'],200);
          }else{
                return response()->json(['status'=>false,'message'=>'No notification found with this id'],401);

          }
        }else{
            $notification = Notification::where('id',$id)->where('receiver_id',Auth::user()->id)->first();
            if($notification){
                $notification->delete();
                return response()->json(['status'=>true,'message'=>'deleted'],200);
            }else{
                return response()->json(['status'=>false,'message'=>'No notification found with this id'],401);

            }

        }
    }else{
        return response()->json(['status'=>false,'message'=>'No notification found with this id'],401);

    }

    }
}
