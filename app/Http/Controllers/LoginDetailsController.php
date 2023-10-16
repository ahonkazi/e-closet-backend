<?php

namespace App\Http\Controllers;

use ALajusticia\AuthTracker\Parsers\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoginDetailsController extends Controller
{
    //
    public function getData(Request $request){
$userAgent = new \Jenssegers\Agent\Agent();
$platform = $userAgent->platform();
$version = $userAgent->version($platform);
$browser = $userAgent->browser();
$ip = $request->ip();


$oauth_access_tokens = DB::table('oauth_access_tokens')->get();
if($oauth_access_tokens){
    return response()->json(['platform'=>$platform,'version'=>$version,'browser'=>$browser,'ip'=>$ip]);;
    
}    
    }
  
}
