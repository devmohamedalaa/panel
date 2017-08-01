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
        return view('admin.setting.index',compact('setting'));
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
            'title_ar'=>'required',
            'title_en'=>'required',
        ],['title_ar.required'=>'يجب ادخال عنوان الموقع بالعربية',
            'title_en.required'=>'يجب ادخال عنوان الموقع بالانجليزية']);
        $setting = Setting::find($id);
        $setting->title_en = $request->title_en;
        $setting->title_ar = $request->title_ar;
        if ($request->hasFile('logo')) {
            //
            $picName = rand(11111,99999).'_'.$request->file('logo')->getClientOriginalName();
            $request->file('logo')->move('admin', 'logo.png');
            $setting->logo = 'logo.png';
            // return 'fff';
        }

        $setting->closeMsg_en = $request->closeMsg_en;
        $setting->closeMsg_ar = $request->closeMsg_ar;
        $setting->address_en = $request->address_en;
        $setting->address_ar = $request->address_ar;
        $setting->tags_en = $request->tags_en;
        $setting->tags_ar = $request->tags_ar;
        $setting->siteDisc_en = $request->siteDisc_en;
        $setting->siteDisc_ar = $request->siteDisc_ar;
        if ($request->has('status')) {
            $setting->status = 1;
        }else{
            $setting->status = 0;
        }
        
        $setting->phone = $request->phone;
        $setting->mobile = $request->mobile;
        $setting->fax = $request->fax;
        $setting->email = $request->email;
       
        $setting->save();
        $request->session()->flash('success', 'تم التعديل بتجاح');
        return back();
    }

    public function show(Setting $setting)
    {
        $setting = Setting::first();
        return view('admin.setting.index',compact('setting'));
    }

   
}
