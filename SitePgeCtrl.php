<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Lang;
class SitePgeCtrl extends Controller
{
    //

    public function contact()
    {
    	$noSide = true;
    	return view('site.pages.contact',compact('noSide'));
    }

    public function contactMail(Request $request)
    {
		
		// dd($request->all());
		$validator = \Validator::make($request->all(),[
			'name'=>'required|max:100||min:3|string',
			'email'=>'required|email',
			'subject'=>'required|min:10',
			'message'=>'required||min:40',
		]);

        if ($validator->fails()) {
            return redirect('contact#contact-form')
                        ->withErrors($validator)
                        ->withInput();
        }	

    	\Mail::send('email.contactClient', ['client'=>$request], function ($m) use($request) {
            $m->from('info@boxstoreseg.com', 'Box Stores');
            $m->to($request->email,$request->name)->subject('Thinks For Contact');
        });
    	$request->session()->flash('success', Lang::get('contact.sendDone'));
        return redirect('contact#contact-form');
    }
}
