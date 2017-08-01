<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Requests\UserR;
use App\User;
use Validator;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::find(\Auth::user()->id);
        return view('user.profile',compact('user'));
    }

    /**
     * Show Traning dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function traning()
    {
        if(\App\User::Pre() == 1){
            $user = User::find(\Auth::user()->id);
            // return $user->traning;
            return view('user.traning.index',compact('user'));
        }
        return view('errors.503');
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
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $user = User::find($id);
        $this->validate($request,[
                'name'=>'required',
                'email'=>'required|email',
                'phone'=>'required',
                'pic'=>'mimes:jpeg,bmp,png',
            ],
            [
                'name.required' =>'يجب ادخال الاسم',
                'email.required' =>'يجب ادخال البردي الالكتروني',
                'phone.required' =>'يجب ادخال رقم الهاتف',
            ]
        );
        if ($request->hasFile('pic')) {
            //
            $picName = rand(11111,99999).'_'.$request->file('pic')->getClientOriginalName();
            $request->file('pic')->move('user/pic', $picName);
            $user->pic = $picName;
        }
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        if(!empty($request->password)){
            $user->password = bcrypt($request->password);
        }
        $request->session()->flash('success', 'تم التعديل بتجاح');
        $user->save();

        return redirect('profile');
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
}
