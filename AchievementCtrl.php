<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Achievement;
class AchievementCtrl extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
            $achievement = Achievement::orderBy('id','desc')->get();
            return view('admin.achievement.index',compact('achievement'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
            return view('admin.achievement.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,Achievement $achievement)
    {
        //
        // dd($request->all());
            $this->validate($request,[
                'title' => 'required',
                'summary' => 'required',
                'content' => 'required',
                'img' => 'required',
            ],Achievement::$ruels);

            $achievement->title = $request->title;
            $achievement->content = $request->content;
            $achievement->summary = $request->summary;
            if ($request->hasFile('img')) {
                $picName = rand(11111,99999).'_'.$request->file('img')->getClientOriginalName();
                $request->file('img')->move('admin/images/achievement',$picName);
                $achievement->img = $picName;
            }
            $achievement->save();
            $request->session()->flash('success', 'تم إضافة انجاز بنجاح');
            return redirect('admin/achievement');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Achievement $achievement)
    {
        //
            return view('admin.achievement.show',compact('achievement'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Achievement $achievement)
    {
        //
            return view('admin.achievement.edit',compact('achievement'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Achievement $achievement)
    {
        //
            $this->validate($request,[
                'title' => 'required',
                'summary' => 'required',
                'content' => 'required',
            ],Achievement::$ruels);

            $achievement->title = $request->title;
            $achievement->content = $request->content;
            $achievement->summary = $request->summary;
            if ($request->hasFile('img')) {
                \File::delete('admin/images/achievement/'.$achievement->img);
                $picName = rand(11111,99999).'_'.$request->file('img')->getClientOriginalName();
                $request->file('img')->move('admin/images/achievement',$picName);
                $achievement->img = $picName;
            }
            $achievement->save();
            $request->session()->flash('success', 'تم تعديل انجاز بنجاح');
            return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Achievement $achievement,Request $request)
    {
        //
        if (in_array('dep_achievement', PreCtrl::pre())) {
            $achievement->delete();
            \File::delete('admin/images/achievement/'.$achievement->img);
            $request->session()->flash('success', 'تم حذف انجاز بنجاح');
            return redirect('admin/achievement');
        }
    }
}
