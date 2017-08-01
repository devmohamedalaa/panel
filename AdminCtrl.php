<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Admin;
class AdminCtrl extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $users = \App\User::where('pre',1)->get();
        $exams = \App\Exam::all()->count();
        $images = \App\Image::all()->count();
        $videos = \App\Video::all()->count();
        $count = \App\User::where('pre',1)->get();
        $s1= []; $s2 = []; $s3 = []; $s4 = [];
        $t1= []; $t2 = []; $t3 = []; $t4 = [];
        $p1= []; $p2 = []; $p3 = []; $p4 = [];
        foreach ($count as  $value) {
            if (in_array('s1', $value->traning)) {
                $s1[] = $value;
            }
            if (in_array('s2', $value->traning)) {
                $s2[] = $value;
            }
            if (in_array('s3', $value->traning)) {
                $s3[] = $value;
            }
            if (in_array('s4', $value->traning)) {
                $s4[] = $value;
            }
            // Team

            if (in_array('t1', $value->traning)) {
                $t1[] = $value;
            }
            if (in_array('t2', $value->traning)) {
                $t2[] = $value;
            }
            if (in_array('t3', $value->traning)) {
                $t3[] = $value;
            }
            if (in_array('t4', $value->traning)) {
                $t4[] = $value;
            }
            // Project
            if (in_array('p1', $value->traning)) {
                $p1[] = $value;
            }
            if (in_array('p2', $value->traning)) {
                $p2[] = $value;
            }
            if (in_array('p3', $value->traning)) {
                $p3[] = $value;
            }
            if (in_array('p4', $value->traning)) {
                $p4[] = $value;
            }
        }
        // return count($s3);
       return view('admin.index',compact('users','exams','images','videos','s1','s2','s3','s4','t1','t2','t3','t4','p1','p2','p3','p4'));
    }

    /**
     * Login Admin.
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        //
        if (auth()->guard('admins')->check()) {
            return redirect('admin/home');
        }   
        return view('adminAuth.login');
    }
    /**
     * Login Admin.
     *
     * @return \Illuminate\Http\Response
     */
    public function postLogin(Request $request)
    {
        //
        $this->validate($request,[
            'email' => 'required|email|max:255',
            'password' => 'required|min:6',
        ]);
        if (\Auth::guard('admins')->attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect('/admin/home');
            
        }
        return view('adminAuth.login');
    }

    /**
     * Register Admin.
     *
     * @return \Illuminate\Http\Response
     */
    public function register()
    {
        //
        return view('adminAuth.register');
    }

    /**
     * Register Admin.
     *
     * @return \Illuminate\Http\Response
     */
    public function postRegister(Request $request)
    {
        //
        $this->validate($request,[
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        if(\Auth::guard('admins')->attempt(['email' => $request->email, 'password' => $request->password])
        ){
            return redirect('admin/home');
        }
        Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
  
        return redirect('admin/home');
    }

    public function logout()
    {
       \Auth::guard('admins')->logout();
        return redirect('/');
    }

    public function contact(Request $request)
    {
        $this->validate($request,['name'=>'required','email'=>'required','subject'=>'required','message'=>'required'],[
                'name.required'=>'يجب ادخال الاسم',
                'email.required'=>'يجب ادخال البريد الالكتروني',
                'subject.required'=>'يجب ادخال الموضوع',
                'message.required'=>'يجب ادخال الرسالة'
            ]);

        $data = ['name'=> $request->name ,
                'email'=> $request->email,
                'subject'=> $request->subject,
                'msg' => $request->message];
        \Mail::send('emails.contact', $data, function ($message) use($request) {      
            $message->to('devmario.95@gmail.com',$request->name)->subject($request->subject);  
        });
        $request->session()->flash('success', 'تمت الارسال بنجاح');
        return back();
    }

}
