<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Center;
class CenterCtrl extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
            $center = Center::orderBy('id','desc')->get();
            return view('admin.center.index',compact('center'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
            return view('admin.center.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,Center $center)
    {
        //
        // dd($request->all());
            $this->validate($request,[
                'title' => 'required',
                'img' => 'required',
                'content' => 'required',
                'about' => 'required',
            ]);

            $center->title = $request->title;
            $center->content = $request->content;
            $center->about = $request->about;
            if ($request->hasFile('img')) {
                $picName = rand(11111,99999).'_'.$request->file('img')->getClientOriginalName();
                $request->file('img')->move('admin/images/center',$picName);
                $center->img = $picName;
            }
            $center->save();
            $request->session()->flash('success', 'تم إضافة خبر بنجاح');
            return redirect('admin/center');
            
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Center $center)
    {
            return view('admin.center.show',compact('center'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Center $center)
    {
        //
            return view('admin.center.edit',compact('center'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Center $center)
    {
        //
            $this->validate($request,[
                'title' => 'required',
                'content' => 'required',
                'about' => 'required',
            ]);

            $center->title = $request->title;
            $center->content = $request->content;
            $center->about = $request->about;
            if ($request->hasFile('img')) {
                \File::delete('admin/images/center/'.$center->img);
                $picName = rand(11111,99999).'_'.$request->file('img')->getClientOriginalName();
                $request->file('img')->move('admin/images/center',$picName);
                $center->img = $picName;
            }
            $center->save();
            $request->session()->flash('success', 'تم تعديل خبر بنجاح');
            return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Center $center,Request $request)
    {
        //
            $center->delete();
            \File::delete('admin/images/center/'.$center->img);
            $request->session()->flash('success', 'تم حذف خبر بنجاح');
            return redirect('admin/center');
    }
}
