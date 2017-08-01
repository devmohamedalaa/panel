<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Donor;
use App\Offers;
class DonorCtrl extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $offer = Offers::where('donor_id',auth()->guard('donors')->user()->id)->get();
        $initiative = [] ;
        foreach ($offer as $o) {
            $initiative[] = \App\Initiative::find($o->initiative_id);
        }
        // return $offer;
        // return $offer->initiative;
        $donor = auth()->guard('donors')->user();

        // return $initiative;
        return view('donor.donor.index',compact('donor','initiative'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        //
        $donor = auth()->guard('donors')->user();

        // return $donor;
        return view('donor.donor.edit',compact('donor'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
        // dd($request->all());
        $this->validate($request,[
            'name'=>'required',
            'email'=>'required',
            'mobile'=>'required',
        ],\App\User::$ruels);

        $donor = Donor::find(auth()->guard('donors')->user()->id);
        $donor->name = $request->name;
        $donor->email = $request->email;
        $donor->mobile = $request->mobile;
        if ($request->hasFile('img')) {
            $picName = rand(11111,99999).'_'.$request->file('img')->getClientOriginalName();
            $request->file('img')->move('site/images/donor/',$picName);
            $donor->img = $picName;
        }
        if (!empty($request->password)) {
            $donor->password = bcrypt($request->password);
        }

        
        $donor->save();
        $request->session()->flash('success', 'تم التعديل  بنجاح');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Login Donor.
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        // //
        if (auth()->guard('donors')->check()) {
            return redirect('donor/profile');
        }   
        return view('donor.auth.login');
    }

    /**
     * Login Donor.
     *
     * @return \Illuminate\Http\Response
     */
    public function postLogin(Request $request)
    {
        // return $request->all();
        $this->validate($request,[
            'email' => 'required|email|max:255',
            'password' => 'required|min:6',
        ],[
            'email.required'=>'يجب ادخال البريد الالكتروني',
            'password.required'=>'يجب ادخال كلمة المرور'
        ]);
        if (\Auth::guard('donors')->attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect('/donor/profile');
            
        }
        $request->session()->flash('error','يجب التاكد من البيانات المدخلة');
        return view('donor.auth.login');
    }
    
    public function showRegistrationForm()
    {
        if (auth()->guard('donors')->check()) {
            return redirect('donor/profile');
        }   
        return view('donor.auth.register');
    }

    /**
     * Login Donor.
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        // return $request->all();
        $this->validate($request,[
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:donors',
            'password' => 'required|min:6|confirmed',
        ],[
            'name.required'=>'يجب ادخال ألاسم',
            'email.required'=>'يجب ادخال البريد الالكتروني',
            'email.unique'=>'البريد الالكتروني مسجل من قبل',
            'password.required'=>'يجب ادخال كلمة المرور',
            'password.confirmed'=>'كلمة المرور ليست متطابقة',
        ]);
        $donor = new Donor();
        $donor->name = $request['name'];
        $donor->email =$request['email'];
        $donor->password =  bcrypt($request['password']);
        $donor->save();
        $this->sendMail($request);
        if(\Auth::guard('donors')->attempt(['email' => $request->email, 'password' => $request->password])){
            return redirect('/donor/profile');
        }
    }

    public function logout()
    {
       \Auth::guard('donors')->logout();
        return redirect('/');
    }

    public function showResetForm()
    {
        return view('donor.auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $this->validate($request,[
            'email'=>'required'
        ],[
            'email.required'=>'يجب ادخال البريد الالكتروني'
        ]);

        $donor = Donor::where('email',$request->email)->first();
        if ($donor) {
            $donor->reset_password = str_random(40);
            $donor->save();
            $this->senMailReset($donor);
            $request->session()->flash('status','تم ارسال رابط تفعيل البريد الالكتروني');
            return back();
        }else{
            $request->session()->flash('notfound','البريد الالكتروني غير مسجل .');
            return back();
        }
    }

    public function reset($email,$token)
    {
        return view('donor.auth.passwords.reset',compact('email','token'));
        
    }
    public function resetDonor(Request $request ,$email,$token)
    {
        $donor = Donor::where(['email'=>$email,'reset_password'=>$token])->first();
        $donor->password = bcrypt($request['password']);
        $donor->reset_password = null;
        $donor->save();
        if ($donor) {
            \Auth::guard('donors')->login($donor);
            return redirect('/');
        }else {
            echo 'error';
        }
    }

    public function senMailReset($data)
    {
        \Mail::send('donor.auth.passwords.sendMail', ['data'=>$data],  function ($m) use($data)  {
        $m->from('info@sahem-csr.com','SAHEM');

        $m->to($data->email,$data->name)->subject('ساهم - استعادة كلمة المرور');
        }); 

    }

    public function sendMail($data)
    {
        \Mail::send('email.sendData', ['data' => $data], function ($m) use ($data) {
            $m->from('devmario.95@gmail.com', 'SAHEM');

            $m->to('m.3laa.95@gmail.com', $data['name'])->subject('تم التسجيل بنجاح');
        });
    }
}
