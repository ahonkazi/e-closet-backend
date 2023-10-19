<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class customerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
if(Auth::user()){
    if(Auth::user()->user_role ==  1){
        return $next($request);
    }else{
        return response()->json(['message'=>'You are not a customer','status'=>false]);
    }
}else{
    return response()->json(['message'=>'You are not logged in','status'=>false]);

}    }
}
