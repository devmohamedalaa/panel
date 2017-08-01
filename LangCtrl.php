<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests;

class LangCtrl extends Controller
{
    //
    public function index(Request $request)
    {
        # code...
        if(!\Session::has('locale')){ 
            \Session::put('locale', Input::get('locale')); 
        }else{ 
            Session::set('locale', Input::get('locale')); 
        } 
        return Redirect::back();
    }

}
