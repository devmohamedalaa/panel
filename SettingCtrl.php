<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Setting;
class SettingCtrl extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $setting = Setting::first();
        return view('admin.setting.setting',compact('setting'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        //
        // return $request->all();
        $this->validate($request,[
            'siteName'=>'required',
        ],Setting::$rules);
        $setting = Setting::find($id);
        $setting->siteName = $request->siteName;
        $setting->siteDisc = $request->siteDisc;
        if ($request->hasFile('logo')) {
            //
            $picName = rand(11111,99999).'_'.$request->file('logo')->getClientOriginalName();
            $request->file('logo')->move('admin', 'logo.png');
            $setting->logo = 'logo.png';
        }

        $setting->phone = $request->phone;
        $setting->email = $request->email;
        $setting->address = $request->address;
        $setting->facebook = $request->facebook;
        $setting->twitter = $request->twitter;
        $setting->youtube = $request->youtube;
        $setting->instagram = $request->instagram;
        $setting->save();
        $request->session()->flash('success', 'تم التعديل بتجاح');
        return view('admin.setting.setting',compact('setting'));
    }

}