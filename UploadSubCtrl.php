<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Subject;
use App\UploadSub;
class UploadSubCtrl extends Controller
{
    /**
     * Display a listing of the resource.
     * /Link userSub
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $subjects = Subject::orderBy('id','desc')->get();
        return view('admin.upSub.index',compact('subjects'));
    }

    /**
     * Link Is userSub/{id}
     * Show Upload User Subject.
     *
     * @return Upload User Subject
     */
    public function userUpSub($id)
    {
        $userUp = [];
        $subjects = Subject::find($id);
        $upUserSub = UploadSub::where('sub_id',$id)->orderBy('id','desc')->get();
        foreach ($upUserSub as  $value) {
            $userUp[] = $value;
        }
        foreach ($userUp as  $v) {
            $u[] = $v->user;
        }
        // return $userUp;
        return view('admin.upSub.indexUserUp',compact('userUp','subjects','u'));
    }

    //  showUserSub/{id}/{subId}/{userId}
    public function showUserUpSub($id,$subId,$userId)
    {
        //

        $upUserSub = UploadSub::where('sub_id',$subId)->where('user_id',$userId)->orderBy('id','desc')->get();
        $subjects = Subject::find($subId);

        // return $upUserSub;
        
        return view('admin.upSub.show',compact('upUserSub','subjects','u','id'));
    }

    public function comment(Request $request,$id){
        // return $request->all();

        $upSub = \App\UploadSub::find($id);
        if (!empty($upSub->c_admin)) {
            $upSub->c_admin = $upSub->c_admin.'|'.$request->c_admin;
        }else{
            $upSub->c_admin = $request->c_admin;
        }
        $upSub->save();
        return back()->with('success','تم الارسال');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        //
        if(\App\User::Pre() == 1){

            $upUserSub = UploadSub::where('sub_id',$id)->where('user_id',auth()->user()->id)->orderBy('id','desc')->get();
            $subjects = Subject::findOrFail($id);

            if(array_search( (int) auth()->user()->address , $subjects->city)  ){
                return view('user.upSub.create',compact('subjects','upUserSub'));
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
            $this->validate($request,['comment'=>'string','file.*'=>'required'],['file.*'=>'يجب ادخال الملف','comment.required'=>'يجب ادخال تعليق','comment.string'=>'التعليق يجب ان يكون نص']);
            $upSub = new \App\UploadSub();
            $upSub->user_id = auth()->user()->id;
            $upSub->sub_id = $id;
            $upSub->comment = $request->comment;

            if ($request->file('file')) {
                $subjects = [];
                foreach ($request->file('file') as $file) {
                    if(!empty($file)){
                        $subName = rand(11111,99999).'_'.$file->getClientOriginalName();
                        $file->move('admin/upSub', $subName);
                        $subjects[] =$subName;    
                    }
                }
                $upSub->file = implode('|', $subjects);
            }
            $upSub->save();
            return back()->with('success','تمت الاضافة بنجاح');
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
        $upSub = UploadSub::find($id);
        $image = explode('|', $upSub->file);
        foreach ($image as  $value) {
            \File::delete('admin/upSub/'.$value);
        }
        $upSub->delete();
        return back();
    }
}
