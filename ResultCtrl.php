<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Result;
use App\User;
use App\Exam;
use Carbon\Carbon;
class ResultCtrl extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        //
        $exam = Exam::find($id);
        $users = User::all();
        foreach ($users as $user) {
            if(in_array( (int) $user->address, $exam->city)){
                $sUser[] = $user; 
            }
        }
        return view('admin.result.index',compact('sUser','exam'));
    }

    public function userResult()
    {
        if(\App\User::Pre() == 1){
            $ans = [];
            $answer = \App\Answer::where('user_id',auth()->user()->id)->get();
            foreach ($answer as  $value) {
                $ans[] = $value->exam_id;
            }
            $exams = Exam::orderBy('id','desc')->get();
            $userExam = [];

            // return $exams;
            foreach ($exams as $sub) {

                if(array_search( (int) auth()->user()->address , $sub->city)){
                    // if($sub->end >= Carbon::now() && !in_array( $sub->id ,$ans)){
                        $userExam[] = $sub; 
                    // }
                }
            }

            // return $ans;
            return view('user.result.index',compact('userExam'));
        }
        return view('errors.503');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($idExam,$idUser)
    {
        //
        return view('admin.result.create',compact('idExam','idUser'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$idExam,$idUser)
    {
        //
        $results = Result::where('user_id',$idUser)->where('exam_id',$idExam)->get();
        if (count($results) == 0 ) {
        $this->validate($request,['result'=>'required'],['result.required'=>'يجب ادخال النتيجة']);
            $result = new Result();
            $result->result = $request->result;
            $result->user_id = $idUser;
            $result->exam_id = $idExam;
            $result->save();
            return redirect('admin/result/'.$idExam)->with('success','تمت الاضافة بنجاح');
        }else{
            return view('errors.503')->with('msg','لايمكن اضافة نتجية لقد تمت الاضافة من قبل');
        }
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
    public function edit($idExam,$idUser)
    {
        //
        $results = Result::where('user_id',$idUser)->where('exam_id',$idExam)->get();
        $result = $results[0];
        return view('admin.result.edit',compact('result','idExam','idUser'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request$request,$idExam,$idUser)
    {
        //
        $results = Result::where('user_id',$idUser)->where('exam_id',$idExam)->get()[0];
        $results->result = $request->result;
        $results->save();
        return redirect('admin/result/'.$idExam)->with('success','تم التعديل بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($idExam,$idUser)
    {
        $results = Result::where('user_id',$idUser)->where('exam_id',$idExam)->get()[0];
        $results->delete();
        return redirect('admin/result/'.$idExam)->with('success','تمت الحذف بنجاح ');
    }
}
