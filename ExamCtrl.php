<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Exam;
use App\Answer;
use Carbon\Carbon;
class ExamCtrl extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $exams = Exam::orderBy('id','desc')->get();
       return view('admin.exam.index',compact('exams'));
    }

    public  function examUser()
    {
        //
       // return auth()->user()->id;
        if(\App\User::Pre() == 1){
            $ans = [];
            $answer = Answer::where('user_id',auth()->user()->id)->get();
            foreach ($answer as  $value) {
                $ans[] = $value->exam_id;
            }
            $exams = Exam::orderBy('id','desc')->get();
            $userExam = [];

            // return $exams;
            foreach ($exams as $sub) {

                if(array_search( (int) auth()->user()->address , $sub->city)){
                    if($sub->end >= Carbon::now() && !in_array( $sub->id ,$ans)){
                        $userExam[] = $sub; 
                    }
                }
            }

            // return $ans;
            return view('user.exam.index',compact('userExam'));
        }
        return view('errors.503');
    }

    public function showExamUser(Exam $exams)
    {
        
        if(\App\User::Pre() == 1){
            $ans = [];
            $answer = Answer::where('user_id',auth()->user()->id)->get();
            foreach ($answer as  $value) {
                $ans[] = $value->exam_id;
            }
            if(array_search( (int) auth()->user()->address , $exams->city) &&  $exams->end >= Carbon::now() &&  !in_array( $exams->id ,$ans) ){

                return view('user.exam.show',compact('exams','city'));
            }
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
        $city = $this->getCity();
        return view('admin.exam.create',compact('city'));
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

        $this->validate($request,['title'=>'required','end'=>'required'],Exam::$ruels);
        $exam = new Exam();
        $exam->title = $request->title;
        $exam->end = date("Y-m-d", strtotime($request->end));
        if ($request->file('file')) {
            $exams = [];
            foreach ($request->file('file') as $file) {
                if(!empty($file)){
                    $subName = rand(11111,99999).'_'.$file->getClientOriginalName();
                    $file->move('admin/exam', $subName);
                    $exams[] =$subName;    
                }
            }
            $exam->file = implode('|', $exams);
        }

        $exam->city = '|'.implode('|', $request->city);
        $exam->save();
        // send Email To All Student has a City Num
        foreach ($request->city as $city) {
            $students = \App\User::where('address','=',$city)->where('pre',1)->get();
            foreach ($students as $student) {
                if ($request->has('smsTitle')) {
                    SMS::sms($student->phone,' تم اضافة امتحان :  '.$exam->title.' ينتهي في :'.\Arabicdatetime::date( strtotime(date("Y-m-d", strtotime($request->end))) , 1 , 'D d - M - Y' ) ,$request->smsTitle);
                }else{
                    SMS::sms($student->phone,' تم اضافة امتحان :  '.$exam->title.' ينتهي في :'.\Arabicdatetime::date( strtotime(date("Y-m-d", strtotime($request->end))) , 1 , 'D d - M - Y' ));
                }
                $data = [
                    'end' =>\Arabicdatetime::date( strtotime(date("Y-m-d", strtotime($request->end))) , 1 , 'D d - M - Y' ),
                    'name'=> $student->name ,
                    'sub'=> $request->title ,
                    'time'=> $student->created_at ];
                if (!empty($student->email)) {   
                    \Mail::send('emails.sendExam',$data,function($message)use($student){      
                        $message->to($student->email,$student->name)->subject('برنامج تأهيل القيادات الشابة ق3!');  
                    });
                }
            }
        }
        
        return redirect('admin/exams')->with('success','تمت الاضافة بنجاح');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Exam $exams)
    {
        // return $exams;
        foreach ($exams->city as $value) {
            if(!empty($value)):
            $city[] = \App\City::find($value);
            endif;
        }

        return view('admin.exam.show',compact('exams','city'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Exam $exams)
    {
        //
        $city = $this->getCity();
        return view('admin.exam.edit',compact('exams','city'));
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

        // return date("Y-m-d", strtotime($request->end));
        $this->validate($request,['title'=>'required'],Exam::$ruels);
        $exam = Exam::find($id);
        $exam->title = $request->title;

        foreach ($request->city as $city) {
            $students = \App\User::where('address','=',$city)->where('pre',1)->get();
            foreach ($students as $student) {
                $data = [
                    'end' =>\Arabicdatetime::date( strtotime(date("Y-m-d", strtotime($request->end))) , 1 , 'D d - M - Y' ),
                    'name'=> $student->name ,
                    'sub'=> $request->title ,
                    'time'=> $student->created_at,
                    'edit'=> 'edit' ];
                if (!empty($student->email)) {   
                    \Mail::send('emails.sendExam',$data,function($message)use($student){      
                        $message->to($student->email,$student->name)->subject('برنامج تأهيل القيادات الشابة ق3!');  
                    });
                }
            }
        }

        if (is_array($request->city)) {
            $exam->city = '|'.implode('|', $request->city);
        }else{ $exam->city = null; }

        if (!empty($request->end)) {
            $exam->end = date("Y-m-d", strtotime($request->end));
        }

        if ($request->file('file')) {

            foreach ($request->file('file') as $file) {
                if(!empty($file)){
                    $subName = rand(11111,99999).'_'.$file->getClientOriginalName();
                    $file->move('admin/exam', $subName);
                    $sub[] =$subName;
                }else{
                    $sub = [];
                }

            }
            $exam->file = $exam->file.'|'.implode('|', $sub);

        }
        $exam->save();
        return back()->with('success','تم التعديل بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Exam $exams)
    {
        //
        $exam = explode('|', $exams->file);
        foreach ($exam as  $value) {
           \File::delete('admin/exam/'.$value);
        }
        $answer = \App\Answer::where('exam_id',$exams->id)->get();
        foreach ($answer as  $value) {
            foreach ($value->file as $v) {
               \File::delete('user/answer/'.$v);
            }
            if (!empty($value)) {
                $value->delete();
            }
        }
        $exams->delete();
        return back();
    }

    public function deleteSub($nameFile,$id){
        $exams = Exam::find($id);
        $exam = explode('|', $exams->file);

        if(($key = array_search($nameFile, $exam)) !== false) {
            \File::delete('admin/exam/'.$exam[$key]);
            unset($exam[$key]);
        }
        $exams->file = implode("|",$exam);
        $exams->save();
        return back();
    } 
}
