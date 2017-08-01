<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;
class CharityCtrl extends Controller
{
    public function userAuth(){
        
        return User::find(auth()->user()->id);

    }
    
    public function index()
    {
        //
        $charity = User::orderBy('id','desc')->where('status',1)->paginate(24);
        $city = \App\City::all();

        // return User::where(['place'=>4,'type'=>1])->get();
        return view('site.charity.all',compact('charity','city'));
        
    }

    public function indexAdmin()
    {
        //
        $charity = User::orderBy('id','desc')->get();
        return view('admin.charity.index',compact('charity'));
        
    }

    public function charity()
    {
        $initiative = \App\Initiative::where('charity_id',auth()->user()->id)->paginate(10);
        $charity = auth()->user();
        return view('site.charity.index',compact('charity','initiative'));
    }

    public function details(User $charity)
    {
        return view('site.charity.details',compact('charity'));
    }

    public function charityStatus(User $charity)
    {

        return $_GET['id'];
        if ($charity->status == 1) {
            $charity->status = 0;
        }else{
            $charity->status = 1;
        }
        $charity->save();
        return $charity->status;
    }

    public function charitySearchName($name)
    {
        $charity = User::where('name', 'like', '%'.$name.'%')->get();
       return view('site.charity.search',compact('charity'));
    }

    public function searchCharity($type = 0 ,$place = 0)
    {
        if ($type != 0 && $place != 0) {
            $charity =  \App\User::where(['place'=>$place,'type'=>$type])->get();
        }elseif ($type == 0 && $place != 0) {
            $charity =  \App\User::where(['place'=>$place])->get();
        }elseif ($type != 0 && $place == 0) {
            $charity =  \App\User::where(['type'=>$type])->get();
        }
        return view('site.charity.search',compact('charity'));
    }

    public function profile()
    {   
        $user = $this->userAuth();
        $city = \App\City::all();
        return view('site.account.profile',compact('user','city'));
    }

    public function profileUpdate(Request $request)
    {
        // dd($request->all());
        $this->validate($request,[
            'name'=>'required',
            'email'=>'required',
            'goal'=>'required',
            'about'=>'required',
            'phone'=>'required',
            'place'=>'required',
            'type'=>'required'
        ],User::$ruels);

        $profile = User::find(auth()->user()->id);

        if ($request->hasFile('img')) {
            $picName = rand(11111,99999).'_'.$request->file('img')->getClientOriginalName();
            $request->file('img')->move('site/images/users/',$picName);
            $profile->img = $picName;
        }

        if (!empty($request->password)) {
            $profile->password = bcrypt($request->password);
        }

        $profile->name = $request->name;
        $profile->about = $request->about;
        $profile->place = $request->place;
        $profile->type =  $request->type;
        $profile->goal = $request->goal;
        $profile->fax = $request->fax;
        $profile->phone = $request->phone;
        $profile->mobile = $request->mobile;
        $profile->save();
        $request->session()->flash('success', 'تم التعديل  بنجاح');
        return back();

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $specialty = \App\Specialty::all();
        $city = \App\City::all();
        $type = \App\Type::all();
        return view('admin.charity.create',compact('specialty','city','type'));
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
        // return 4;
        $this->validate($request,[
            'name'=>'required',
            'email'=>'required',
            // 'goal'=>'required',
            // 'about'=>'required',
            'phone'=>'required',
            'place'=>'required',
            'specialty_id'=>'required',
            // 'type'=>'required'
        ],User::$ruels);
        $profile = new User;

        // if ($request->hasFile('img')) {
        //     $picName = rand(11111,99999).'_'.$request->file('img')->getClientOriginalName();
        //     $request->file('img')->move('site/images/users/',$picName);
        //     $profile->img = $picName;
        // }
        $profile->name = $request->name;
        $profile->email = $request->email;
        $profile->specialty_id = $request->specialty_id;
        $profile->place = $request->place;
        $profile->phone = $request->phone;
        $profile->type =  $request->type;
        $profile->slug = rand(0000,9999).'-'.str_replace(' ', '-',$request->name);
        if (auth()->guard('admins')) {
            $profile->status = 1;
        }else{
            $profile->status = 0;
        }
        // return $profile;
        $this->sendMail($request);
        $profile->save();
        return redirect('admin/charity')->with('success','تمت الاضافة بنجاح'); 
    }

    public function sendMail($data)
    {
        \Mail::send('email.sendData', ['data' => $data], function ($m) use ($data) {
            $m->from('devmario.95@gmail.com', 'SAHEM');

            $m->to('m.3laa.95@gmail.com', $data['name'])->subject('تم التسجيل بنجاح');
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $charity)
    {
        //
        $specialty = \App\Specialty::all();
        $city = \App\City::all();
        $type = \App\Type::all();
        return view('admin.charity.show',compact('charity','specialty','city','type'));
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
    public function update(Request $request, $id)
    {
        //
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
