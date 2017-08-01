<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Exam;
use App\Answer;
use App\Subject;
class AnswerCtrl extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $exams = Exam::orderBy('id','desc')->get();
        return view('admin.answer.index',compact('exam','id','exams'));
    }

    public function userAnswer($id)
    {
        //
        // return $id;
        if(\App\User::Pre() == 1){
            $ans = [];
            $answer = Answer::where('user_id',auth()->user()->id)->get();
            foreach ($answer as  $value) {
                $ans[] = $value->exam_id;
            }
            $exam = Exam::find($id);
            if( !in_array( $id ,$ans) && $exam->end >= \Carbon\Carbon::now() && array_search( (int) auth()->user()->address , $exam->city) ){
                return view('user.answer.create',compact('exam','id'));
            }
            return view('errors.503');
        }
        return view('errors.503');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$id)
    {
        //
        if(\App\User::Pre() == 1){
            $this->validate($request,['file.*'=>'required'],['file.*'=>'يجب ادخال المف']);
            $answer = new Answer();
            $answer->exam_id = $id;
            $answer->user_id = auth()->user()->id;

            if ($request->file('file')) {
                foreach ($request->file('file') as $file) {
                    $fileName = rand(11111,99999).'_'.$file->getClientOriginalName();
                    $file->move('user/answer', $fileName);
                    $files[] =$fileName;
                }
            }
            $answer->file = implode('|', $files);
            $answer->save();
            return redirect('exams')->with('success','تمت الاضافة بنجاح'); 
        }
        return view('errors.503');
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
        $users =[];
        $answer = Answer::where('exam_id',$id)->orderBy('id','desc')->get();
        foreach ($answer as $value) {
            $users[] = $value->user;
        }
        $exam = Exam::find($id);
        // return $answer;
        return view('admin.answer.indexUserAns',compact('users','answer','exam'));
    }
        
    // Show User Answer In Select Exam ..
    public function showUsersAnswer($examId ,$userId)
    {
        //
        $answers = Answer::where('exam_id',$examId)->where('user_id',$userId)->first();


        return view('admin.answer.show',compact('answers'));
    }    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($examId,$userId)
    {
        //
        $answers = Answer::where('exam_id',$examId)->where('user_id',$userId)->first();
        foreach ($answers->file as  $value) {
            \File::delete('user/answer/'.$value);
        }
        $answers->delete();
        return back()->with('success','تمت الاضافة بنجاح');
    }
}
