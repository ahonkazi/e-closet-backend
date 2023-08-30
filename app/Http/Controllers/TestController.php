<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use function Termwind\renderUsing;

class TestController extends Controller
{
    //
    
    public function get(Request $request){
        return response()->json(['reponse'=>$request->all()]);
    }
}
