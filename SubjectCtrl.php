<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Subject;
use Carbon\Carbon;
class SubjectCtrl extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $subjects = Subject::orderBy('id','desc')->get();
        return view('admin.subject.index',compact('subjects'));
    }

    public function subUser()
    {
        //
        if(\App\User::Pre() == 1){
            $subjects = Subject::orderBy('id','desc')->get();
            $userSub = [];
            foreach ($subjects as $sub) {
                if(array_search( (int) auth()->user()->address , $sub->city)  ){
                    $userSub[] = $sub; 
                }
            }
            return view('user.subject.index',compact('userSub'));

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
        return view('admin.subject.create',compact('city'));
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
        $this->validate($request,['name'=>'required','city'=>'required','subject.*'=>'required'],Subject::$ruels);
        $subject = new Subject();
        $subject->name = $request->name;

        if ($request->file('subject')) {
            $subjects = [];
            foreach ($request->file('subject') as $file) {
                if(!empty($file)){
                    $subName = rand(11111,99999).'_'.$file->getClientOriginalName();
                    $file->move('admin/subject', $subName);
                    $subjects[] =$subName;    
                }
            }
            $subject->subject = implode('|', $subjects);
        }

        $subject->city = '|'.implode('|', $request->city);
        $subject->save();
        // send Email To All Student has a City Num
        foreach ($request->city as $city) {
            $students = \App\User::where('address','=',$city)->where('pre','1')->get();

            foreach ($students as $student) {
                
                if ($request->has('smsTitle')) {
                    SMS::sms($student->phone,' تم اضافة مادة : '.$subject->name,$request->smsTitle);
                }else{
                    SMS::sms($student->phone,' تم اضافة مادة : '.$subject->name);
                }

                $data = [
                    'name'=> $student->name ,
                    'sub'=> $request->name ,
                    'time'=> (new Carbon($student->created_at))->toDayDateTimeString() ];
                if (!empty($student->email)) {
                    \Mail::send('emails.sendNewSub',$data,function($message)use($student){      
                        $message->to($student->email,$student->name)->subject('مرحبا!');  
                    });
                }    
            }
        }
        // return $students;
        return redirect('admin/subjects')->with('success','تمت الاضافة بنجاح');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Subject $subjects)
    {
        foreach ($subjects->city as $value) {
            if(!empty($value)):
            $city[] = \App\City::find($value);
            endif;
        }
        return view('admin.subject.show',compact('subjects','city'));
    }

    
    public function showSubUser(Subject $subjects)
    {
        //
        if(\App\User::Pre() == 1){
            if(array_search( (int) auth()->user()->address , $subjects->city)  ){
                return view('user.subject.show',compact('subjects','city'));
            }
            return view('errors.503');
        }
        return view('errors.503');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Subject $subjects)
    {
        //
        $city = $this->getCity();
        return view('admin.subject.edit',compact('subjects','city'));
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
        $this->validate($request,['name'=>'required'],Subject::$ruels);
        $subject = Subject::find($id);
        $subject->name = $request->name;
        if (is_array($request->city)) {
            $subject->city = '|'.implode('|', $request->city);
        }else{ $subject->city = null; }
        if ($request->file('subject')) {

            foreach ($request->file('subject') as $file) {
                if(!empty($file)){
                    $subName = rand(11111,99999).'_'.$file->getClientOriginalName();
                    $file->move('admin/subject', $subName);
                    $sub[] =$subName;
                }else{
                    $sub = [];
                }
            }
            $subject->subject = $subject->subject.'|'.implode('|', $sub);

        }
        $subject->save();
        return back()->with('success','تم التعديل بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subject $subjects)
    {
        //
        $subject = explode('|', $subjects->subject);
        foreach ($subject as  $value) {
            \File::delete('admin/subject/'.$value);
        }
        $subjects->delete();
        return back();
    }

    public function deleteSub($nameSub,$id){
        $subjects = Subject::find($id);
        $subject = explode('|', $subjects->subject);

        if(($key = array_search($nameSub, $subject)) !== false) {
            \File::delete('admin/subject/'.$subject[$key]);
            unset($subject[$key]);
        }
        $subjects->subject = implode("|",$subject);
        $subjects->save();
        return back();
    } 
}
