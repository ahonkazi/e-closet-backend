<?php

namespace App\Http\Middleware;

use App\Models\AdminList;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsApprovedAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::user()){
            if(Auth::user()->user_role == 3){
                if(Auth::user()->is_approved){
                    return $next($request);

                }else{
                    return response()->json(['message'=>'You are not approved','status'=>false]);

                }
            }else{
                return response()->json(['message'=>'You are not an admin','status'=>false]);
            }
        }else{
            return response()->json(['message'=>'You are not logged in','status'=>false]);

        }
    }
}
