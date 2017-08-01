<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Message;
class MessageCtrl extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $messages = Message::orderBy('id','desc')->paginate(20);
        return view('admin.message.index',compact('messages'));
    }

    public function msgUser()
    {
        //
        if(\App\User::Pre() == 1){
            $messages = Message::orderBy('id','desc')->get();
            $userMsg = Message::orderBy('id','desc')->where('user_id',auth()->user()->id)->get();
            // return $messages;
            // $userMsg = [];
            foreach ($messages as $msg) {
                if(  array_search( (int) auth()->user()->address , $msg->city)  ){
                    $userMsg[] = $msg; 
                }
            }
            // array($userMsg);
            // return $userMsg;
            return view('user.message.index',compact('userMsg'));
        }
        return view('errors.503');
    }

    public function getCity(){
        return \App\City::all();
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $city = $this->getCity();
        return view('admin.message.create',compact('city'));
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
        $this->validate($request,['message'=>'required',
           'city'=>'required'],Message::$ruels);
        

        $message = new Message();
        $message->message = $request->message;
        $message->city = '|'.implode('|', $request->city);
        $message->save();
        
        foreach (\App\User::all() as $user) {
            if (in_array( (int) $user->address, $request->city)) {
                $nMsg = new \App\Nmessage();
                $nMsg->user_id = $user->id;
                $nMsg->message_id = $message->id;
                $nMsg->save();
            }
        }
        // send Email To All user has a City Num
        foreach ($request->city as $city) {

            if ($request->has('pre') && $request->has('user')) {
                $students = \App\User::where('address','=',$city)->get();
            }elseif($request->has('pre')){
                $students = \App\User::where('address','=',$city)->where('pre','1')->get();
            }elseif($request->has('user')){
                $students = \App\User::where('address','=',$city)->where('pre','0')->get();
            }else{
                $students = \App\User::where('address','=',$city)->where('pre','1')->get();
            }

            foreach ($students as $student) {
                if ($request->has('sms')) { 
                    if ($request->has('smsTitle')) {
                        SMS::sms($student->phone,$request->message,$request->smsTitle); 
                    }else{
                        SMS::sms($student->phone,$request->message); 
                    }
                }
                if ($request->has('email')) {
                    $data = [
                        'name'=> $student->name ,
                        'sub'=> $request->message ,
                        'time'=> $student->created_at ];
                    if (!empty($student->email)) {    
                        if ($request->has('smsTitle')) {
                            \Mail::send('emails.sendMsg',$data,function($message)use($student,$request){      
                                $message->to($student->email,$student->name)->subject($request->smsTitle);  
                            });
                        }else{
                            \Mail::send('emails.sendMsg',$data,function($message)use($student){      
                                $message->to($student->email,$student->name)->subject('برنامج تأهيل القيادات الشابة ق3');  
                            });
                        }
                    }
                }
            }
        }
        return redirect('admin/messages');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Message $messages)
    {
        //
        return view('admin.message.show',compact('messages'));
    }

    public function showUserMsg(Message $messages)
    {
        if(\App\User::Pre() == 1){
            // if(array_search( (int) auth()->user()->address , $messages->city)  ){
                $msg = \App\Nmessage::where('user_id',auth()->user()->id)->where('message_id',$messages->id)->get();
                foreach ($msg as $value) {
                    if ($value->status == 0) {
                        $value->status = 1;
                        $value->save();
                    }
                }
                $replay = \App\ReplayMsg::where('user_id',auth()->user()->id)->where('message_id',$messages->id)->get();
                // return $replay;
                return view('user.message.show',compact('messages','replay'));
            // }
            return view('errors.503');
        }
        return view('errors.503');
    }

    public function replay(Request $request,$id)
    {
        $this->validate($request,['replay'=>'required'],['replay.required'=>'يجب ادخال الرد']);
        $rMsg = new \App\ReplayMsg();
        $rMsg->message_id = $id;
        $rMsg->user_id = (int) auth()->user()->id;
        $rMsg->replay = $request->replay;
        $rMsg->save();
        $request->session()->flash('success', 'تم الارسال بنجاح');
        return back();
    }

    public function showSelectUserMsg()
    {
        $city = $this->getCity();
        return view('admin.message.msgUser',compact('city'));
    }

    public function selectUserMsg(Request $request)
    {
        $this->validate($request,[
           'city'=>'required'],Message::$ruels);
        foreach ($request->city as $city) {
            $students = \App\User::where('address','=',$city)->where('pre',$request->pre)->get();
        }
        return view('admin.message.sendSelectUserMsg',compact('students'));
    }

    public function sendSelectUserMsg(Request $request)
    {
        //return $request->all();
        $this->validate($request,['message'=>'required',
           'users'=>'required'],Message::$ruels);
        $message = new Message();
        $message->message = $request->message;
        $message->save();

        foreach (\App\User::all() as $user) {
            if (in_array( (int) $user->id, $request->users)) {
                // $nMsg = new \App\Nmessage();
                // $nMsg->user_id = $user->id;
                // $nMsg->message_id = $message->id;
                // $nMsg->save();
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
            }
        }
        $request->session()->flash('success', 'تم الارسال بنجاح');
        return redirect('admin/selectUserCity');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Message $messages)
    {
        //
        $nMsg = \App\Nmessage::where('message_id',$messages->id)->get();
        foreach ($nMsg as $m) {
           $m->delete();
        }
        $messages->delete();

        return redirect('admin/messages');
    }
}
