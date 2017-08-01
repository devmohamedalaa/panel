<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;
use App\Message;
class StudentCtrl extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // ->where('pre','1')
        $students = User::orderBy('id','desc')->where('pre',1)->paginate(20);
        $city = \App\City::all();

        return $students;
        return view('admin.student.index',compact('students','city'));

    }

    /**
     * Display a listing of Users.
     *
     * @return \Illuminate\Http\Response
     */
    public function user()
    {
        // ->where('pre','1')
        $students = User::orderBy('id','desc')->where('pre',0)->paginate(20);
        $city = \App\City::all();
        // return $students;
        return view('admin.student.index',compact('students','city'));

    }

    public function showStuByCity(Request $request)
    {
        $students = User::orderBy('id','desc')->where('address',$request->city)->where('pre',1)->get();
        $city = \App\City::all();
        $select = $request->city;
        return view('admin.student.byCity',compact('students','city','select'));
        
    }
    public function showUserByCity(Request $request)
    {
        $students = User::orderBy('id','desc')->where('address',$request->city)->where('pre',0)->get();
        $city = \App\City::all();
        $select = $request->city;
        return view('admin.student.byCity',compact('students','city','select'));
        
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $city = \App\City::all();
        return view('admin.student.create',compact('city'));
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
                'email'=>'required|email|unique:users',
            ],
            [
                'name.required' =>'يجب ادخال الاسم',
                'email.required' =>'يجب ادخال البريد الالكتروني',
                'email.email' =>'يجب ادخال البردي الالكتروني بطريقة صحيحة',
                'email.unique' =>'البريد الالكتروني مسجل من قبل',
            ]
        );
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->address = $request->address;
        $password = 'qaf3'.mt_rand(100000,999999);
        if($request->has('pre') ){
            $user->pre = 1;  
            $user->password = bcrypt($password);
        } 
        if($request->has('traning') ) $user->traning = implode('|', $request->traning);
        if( !empty($request->phone) ) {
            $user->phone = $request->phone;
            SMS::sms($user->phone,'اهلا - '.$user->name.' تمت اضافتك بنجاح البريد الالكتروني الخاص بك  : '.$user->email. ' كلمة المرور الخاصة بك : '. $password );        
        }
        $user->save();
        $data = ['name'=> $user->name ,
                'email'=> $user->email,
                'password'=> $password];   
        \Mail::send('emails.sendData', $data, function ($message) use($request) {      
            $message->to($request->email,$request->name)->subject('برنامج تأهيل القيادات الشابة ق3!');  
        });
        $request->session()->flash('success', 'تمت الاضافة بنجاح');
        return redirect('admin/students');
    }
    // register
    public function reg(Request $request,$id)
    {   
        $this->validate($request,[
                'name'=>'required|min:5|string',
                'email'=>'required|email|unique:users',
                'birthday'=>'required|min:5',
                'city'=>'required|min:3|string',
                'current_work'=>'required|min:5|string',
                'extra_work'=>'required|min:5|string',
                'civil_id'=>'required|min:5',
                'phone'=>'required|min:5',
                'level_en'=>'required|min:5|string',
                'qualified_semesters'=>'required|min:5|string',
                'major_academic'=>'required|min:5|string',
                'entity_qualified'=>'required|min:5|string',
                'qualifications_community'=>'required|min:5|string',
                'reviews_community'=>'required|min:5|string',
                'roles_community'=>'required|min:5|string',
                'memberships'=>'required|min:5|string',
                'community_projects'=>'required|min:5|string',
                'achievements_community'=>'required|min:5|string',
            ],
            User::$ruels
        );
        $request->merge(['address'=>$id]);
        User::create($request->all());
        $request->session()->flash('success', 'تم التسجيل بنجاح');
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $students)
    {
        //
        return view('admin.student.show',compact('students'));
    }
    // 
    public function sendMsg($id)
    {
        $user = User::find($id);
        return view('admin.student.senMsg',compact('user'));
    }
    // 
    public function send(Request $request,$id)
    {
        $user = User::find($id);
        $this->validate($request,['message'=>'required',
        ],Message::$ruels);
        $message = new Message();
        $message->message = $request->message;
        $message->user_id = $user->id;
        $message->save();

        $nMsg = new \App\Nmessage();
        $nMsg->user_id = $user->id;
        $nMsg->message_id = $message->id;
        $nMsg->save();
        if ($request->has('sms')) { 
            if ($request->has('smsTitle')) {
                SMS::sms($user->phone,$request->message,$request->smsTitle); 
            }else{
                SMS::sms($user->phone,$request->message); 
            } 
        }
        if ($request->has('email')) {
            $data = [
                'name'=> $user->name ,
                'sub'=> $request->message ,
                'time'=> $user->created_at ];
            if (!empty($user->email)) {    
                if ($request->has('smsTitle')) {
                    \Mail::send('emails.sendMsg',$data,function($message)use($user,$request){      
                        $message->to($user->email,$user->name)->subject($request->smsTitle);  
                    });
                }else{
                    \Mail::send('emails.sendMsg',$data,function($message)use($user){      
                        $message->to($user->email,$user->name)->subject('برنامج تأهيل القيادات الشابة ق3');  
                    });
                }
            }
        }

        $request->session()->flash('success', 'تم الارسال بنجاح');
        return back();
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $students)
    {
        $city = \App\City::all();
        return view('admin.student.edit',compact('students','city'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,User $students)
    {
        //
        //return $request->all();
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
        $students->name = $request->name;
        $students->email = $request->email;
        $students->address = $request->address;
        $students->phone = $request->phone;
        //return $request->all();

        if($request->has('traning')){
            $students->traning = implode('|', $request->traning);
        }else{
            $students->traning = null;
        }

        $password = 'qaf3'.mt_rand(100000,999999);
        $data = ['name'=> $students->name ,
                'email'=> $students->email,
                'password'=> $password];   
        
        if($request->has('cPass') && $students->pre == 0 ){
            $students->password = bcrypt($password);
            \Mail::send('emails.sendData', $data, function ($message) use($request) {      
                $message->to($request->email,$request->name)->subject('برنامج تأهيل القيادات الشابة ق3!');  
            });
        }

        if($request->has('pre') && $request->has('cPass') ){
            $students->password = bcrypt($password);
            \Mail::send('emails.sendData', $data, function ($message) use($request) {      
                $message->to($request->email,$request->name)->subject('برنامج تأهيل القيادات الشابة ق3!');  
            });
        }

        if($request->has('pre') ){
            $students->pre = 1;  
        }else{
            $students->pre = 0;
        }
        $request->session()->flash('success', 'تم التعديل بتجاح');
        $students->save();
        
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $students)
    {
        //
        $students->delete();
        return back();
    }

    public function delete($id)
    {
        //
        User::find($id)->delete();
        return redirect('admin/users');
    }


    public function search(Request $request)
    {
        $students = User::where('name','like','%'.$request->search.'%')->orWhere('phone','like','%'.$request->search.'%')->orWhere('email','like','%'.$request->search.'%')->paginate(20);
        return view('admin.student.showSrerch',compact('students'));
    }

    public function searchAjax(Request $request)
    {
        $students = User::where('name','like','%'.$request->keywords.'%')->orWhere('phone','like','%'.$request->keywords.'%')->orWhere('email','like','%'.$request->keywords.'%')->get();
        return view('admin.searchR',compact('students'));
    }

    public function searchCityAjaxR(Request $request){
        $students = User::orderBy('id','desc')->where('address',$request->keywords)->where('pre',1)->paginate(20);
        $city = \App\City::all();
        return view('admin.searchCityR',compact('students','city'));

        // return '$request->keyword';
        $students = User::where('address',$request->keywords)->get();
        return view('admin.searchR',compact('students'));
    }
}
