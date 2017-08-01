<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Social;
class SocialCtrl extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $socials = Social::orderBy('id','desc')->get();
        return view('admin.social.index',compact('socials'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.social.create');

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
            'link'=>'required',
            'img'=>'required',
        ],['img.required'=>'يجب ادخال صورة الموقع ',
            'link.required'=>'يجب ادخال رابط الموقع ']);
        $social = new Social();
        if ($request->hasFile('img')) {
            //
            $picName = rand(11111,99999).'_'.$request->file('img')->getClientOriginalName();
            $request->file('img')->move('admin/social', $picName);
            $social->img = $picName;
        }
        
        $social->link = $request->link;
        $social->save();
        $request->session()->flash('success', 'تمت الاضافة بنجاح');
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Social $socials)
    {
        //
        return view('admin.social.show',compact('socials'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Social $socials)
    {
        //
        // return $socials;
        return view('admin.social.edit',compact('socials'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Social $socials)
    {
        //
        // return $request->all();
        $this->validate($request,[
            'link'=>'required',
        ],['link.required'=>'يجب ادخال رابط الموقع']);
        if ($request->hasFile('img')) {
            \File::delete('admin/social/'.$socials->img);
            $picName = rand(11111,99999).'_'.$request->file('img')->getClientOriginalName();
            $request->file('img')->move('admin/social', $picName);
            $socials->img = $picName;
        }
        
        $socials->link = $request->link;
        $socials->save();
        $request->session()->flash('success', 'تم التعديل بنجاح');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Social $socials)
    {
        //
        $socials->delete();
        \File::delete('admin/social/'.$socials->img);
        $request->session()->flash('success', 'تم الحذف بنجاح');
        return back();
    }
}
