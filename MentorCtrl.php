<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Mentor;
class MentorCtrl extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $mentors = Mentor::orderBy('id','desc')->get();
        return view('admin.mentor.index',compact('mentors'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $us = \App\User::where('pre',1)->get();
        $mentors = Mentor::all();
        foreach ($mentors as  $mentor) {
            foreach ($mentor->user_id as $value) {
                $id[] = $value;
            }

        }
        $users = [];
        foreach ($us as $user) {
            if (!in_array($user->id, $id)) {
                $users[] = $user;
            }
        }
        // return $users;
        return view('admin.mentor.create',compact('users'));
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
        // return $request->all();
        $this->validate($request,[
                'name'=>'required',
                'email'=>'required|email|unique:mentors',
            ],
            [
                'name.required' =>'يجب ادخال الاسم',
                'email.required' =>'يجب ادخال البريد الالكتروني',
                'email.email' =>'يجب ادخال البردي الالكتروني بطريقة صحيحة',
                'email.unique' =>'البريد الالكتروني مسجل من قبل',
            ]
        );
        $mentor = new Mentor();
        $mentor->name = $request->name;
        $mentor->email = $request->email;
        if ($request->has('user_id')) {
            $mentor->user_id = '|'.implode('|', $request->user_id);
        }
        $password = 'qaf3'.mt_rand(100000,999999);
        $mentor->password = bcrypt($password);
        $mentor->save();
        
        $data = ['name'=> $mentor->name ,
                'email'=> $mentor->email,
                'password'=> $password];   
        \Mail::send('emails.sendMentorData', $data, function ($message) use($request) {      
            $message->to($request->email,$request->name)->subject('برنامج تأهيل القيادات الشابة ق3!');   
        });
        $request->session()->flash('success', 'تمت الاضافة بنجاح');
        return redirect('admin/mentors');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Mentor $mentors)
    {
        //
        $users = \App\User::where('pre',1)->get();
        return view('admin.mentor.show',compact('mentors','users'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        $user = Mentor::find(\Auth::guard('mentors')->user()->id);
        return view('mentor.profile',compact('user'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request,$id)
    {
        // return $request->all();
        $user = Mentor::find($id);
        $this->validate($request,[
                'name'=>'required',
                'email'=>'required|email',
                'pic'=>'mimes:jpeg,bmp,png',
            ],
            [
                'name.required' =>'يجب ادخال الاسم',
                'email.required' =>'يجب ادخال البردي الالكتروني',
            ]
        );
        if ($request->hasFile('pic')) {
            //
            $picName = rand(11111,99999).'_'.$request->file('pic')->getClientOriginalName();
            $request->file('pic')->move('mentor/pic', $picName);
            $user->pic = $picName;
        }
        $user->name = $request->name;
        $user->email = $request->email;
        if(!empty($request->password)){
            $user->password = bcrypt($request->password);
        }
        $request->session()->flash('success', 'تم التعديل بتجاح');
        $user->save();

        return redirect('mentor/home');
    }

    public function student()
    {
        //
        $mentor = Mentor::find(auth()->guard('mentors')->user()->id);
        // return $mentor;
        $users = \App\User::where('pre',1)->get();
        foreach ($mentor->user_id as $value) {
            $students[] = \App\User::where('pre',1)->find($value);
        }
        // return $students;
        return view('mentor.student.index',compact('mentors','students'));
    }

    public function studentShow($id)
    {
        //
        $mentor = Mentor::find(auth()->guard('mentors')->user()->id);
        // return $mentor;
        $students = \App\User::where('pre',1)->find($id);
        if (!empty($students)) {
            if (in_array($students->id, $mentor->user_id)) {
                return view('mentor.student.show',compact('mentors','students'));
            }
            return view('errors.503');
        }
        return view('errors.503');
    }

    public function studentEdit($id)
    {
        //
        $mentor = Mentor::find(auth()->guard('mentors')->user()->id);
        // return $mentor;
        $students = \App\User::where('pre',1)->find($id);
        if (!empty($students)) {
            if (in_array($students->id, $mentor->user_id)) {
                return view('mentor.student.edit',compact('mentors','students'));
            }
            return view('errors.503');
        }
        return view('errors.503');
    }

    public function studentUpdate(Request $request,$id)
    {
        //
        // return $request->all();
        $mentor = Mentor::find(auth()->guard('mentors')->user()->id);
        $students = \App\User::where('pre',1)->find($id);
        if (!empty($students)) {
            if (in_array($students->id, $mentor->user_id)) {
                if($request->has('traning')){
                    $students->traning = implode('|', $request->traning);
                }else{
                    $students->traning = null;
                }

                $request->session()->flash('success', 'تم التعديل بتجاح');
                $students->save();
                return back();
            }
            return view('errors.503');
        }
        return view('errors.503');
    }

    // Upload Student Subject
    public function subject()
    {
        //
        $subjects = \App\Subject::orderBy('id','desc')->get();
        return view('mentor.upSub.index',compact('subjects'));

    }

    public function userUpSub($id)
    {

        $mentor = Mentor::find(auth()->guard('mentors')->user()->id);
        // return $mentor;
        $users = \App\User::where('pre',1)->get();
        foreach ($mentor->user_id as $value) {
            $students[] = \App\User::where('pre',1)->find($value);
        }

        // 
        $userUp = [];
        $getuserUp = [];
        $subjects = \App\Subject::find($id);
        $upUserSub = \App\UploadSub::where('sub_id',$id)->orderBy('id','desc')->get();
        foreach ($upUserSub as  $value) {
            $userUp[] = $value;
        }
        foreach ($students as $s) {
            foreach ($userUp as $value) {
                if ($s->id ==  $value->user_id) {
                    $getuserUp[] = $value;
                }
            }
        }
        return view('mentor.upSub.indexUserUp',compact('getuserUp','subjects','u'));

    }
    
    public function showUserUpSub($id,$subId,$userId)
    {
        //

        $upUserSub = \App\UploadSub::where('sub_id',$subId)->where('user_id',$userId)->orderBy('id','desc')->get();
        $subjects = \App\Subject::find($subId);

        // return $upUserSub;
        
        return view('mentor.upSub.show',compact('upUserSub','subjects','u','id'));
    }

    // Message Route
    public function indexMsg()
    {
        $messages = \App\Message::orderBy('id','desc')->where('mentor_id',auth()->guard('mentors')->user()->id)->paginate(20);
        return view('mentor.message.index',compact('messages'));
    }

    public function showMsg($id)
    {
        //
        $messages = \App\Message::find($id);
        $replay = \App\ReplayMsg::where('message_id',$messages->id)->get();
        // return $replay;
        return view('mentor.message.show',compact('messages','replay'));
    }

    public function replay(Request $request,$id)
    {
        $this->validate($request,['replay'=>'required'],['replay.required'=>'يجب ادخال الرد']);
        $messages = \App\Message::find($id);
        $rMsg = new \App\ReplayMsg();
        $rMsg->message_id = $id;
        $rMsg->user_id = $messages->user_id;
        $rMsg->replay = $request->replay;
        $rMsg->mentor = 1;
        $rMsg->save();
        $request->session()->flash('success', 'تم الارسال بنجاح');
        return back();
    }

    public function sendMsg($id)
    {
        $user = \App\User::find($id);
        return view('mentor.student.senMsg',compact('user'));
    }
    // 
    public function send(Request $request,$id)
    {
        $user = \App\User::find($id);
        $this->validate($request,['message'=>'required',
        ],\App\Message::$ruels);
        $message = new \App\Message();
        $message->message = $request->message;
        $message->user_id = $user->id;
        $message->mentor_id = auth()->guard('mentors')->user()->id;
        $message->save();

        $nMsg = new \App\Nmessage();
        $nMsg->user_id = $user->id;
        $nMsg->message_id = $message->id;
        $nMsg->save();
        $request->session()->flash('success', 'تم الارسال بنجاح');
        return back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Mentor $mentors)
    {
        //
        $users = \App\User::where('pre',1)->get();
        return view('admin.mentor.edit',compact('mentors','users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Mentor $mentors)
    {
        //
        $this->validate($request,[
                'name'=>'required',
                'email'=>'required|email',
            ],
            [
                'name.required' =>'يجب ادخال الاسم',
                'email.required' =>'يجب ادخال البردي الالكتروني',
                'email.email' =>'يجب ادخال البردي الالكتروني بطريقة صحيحة',
            ]
        );
        $mentors->name = $request->name;
        $mentors->email = $request->email;
        //return $request->all();
        if (!empty($request->password)) {
            $mentors->password = bcrypt($request->password);
        }
        if($request->has('user_id')){
            $mentors->user_id = implode('|', $request->user_id);
        }else{
            $mentors->user_id = null;
        }
        $request->session()->flash('success', 'تم التعديل بتجاح');
        $mentors->save();

        return back();

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Mentor $mentors)
    {
        //
        $mentors->delete();
        return back();
    }


    /**
     * Login Mentor.
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        //
        if (auth()->guard('mentors')->check()) {
            return redirect('mentor/home');
        }   
        return view('mentorAuth.login');
    }

    /**
     * Login Mentor.
     *
     * @return \Illuminate\Http\Response
     */
    public function postLogin(Request $request)
    {
        //
        // return $request->all();
        $this->validate($request,[
            'email' => 'required|email|max:255',
            'password' => 'required|min:6',
        ],['email.required'=>' يجب ادخال البريد الالكتروني ','password.required'=>'يجب ادخال كلمة المرور','password.min.6'=>'علي الاقل 6 مدخلات']);
        if (\Auth::guard('mentors')->attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect('/mentor/home');
            
        }
        return view('mentorAuth.login');
    }
    /**
     * Logout Mentor.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
       \Auth::guard('mentors')->logout();
        return redirect('/');
    }
}
