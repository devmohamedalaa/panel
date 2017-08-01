<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Brand;
class BrandCtrl extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $brand = Brand::all();
        return view('admin.brand.index',compact('brand'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.brand.create');
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
        // dd($request->all());
        $this->validate($request,[
            'name_ar'=>'required',
            'name_en'=>'required',
            'img'=>'required'
            ],[
                'name_ar.required'=>'يجب ادخال الاسم بالعربية',
                'name_en.required'=>'يجب ادخال الاسم بالانجليزية',
                'img.required'=>'يجب ادخال شعار الماركة'
            ]);
        $brand = new Brand();
        $brand->name_ar = $request->name_ar;
        $brand->name_en = $request->name_en;
        if ($request->hasFile('img')) {
            $picName = rand(11111,99999).'_'.$request->file('img')->getClientOriginalName();
            $request->file('img')->move('admin/brands',$picName);
            $brand->img = $picName;
        }
        $brand->save();
        $request->session()->flash('success','تمت اضافة ماركة بنجاح');
        return redirect('admin/brand');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Brand $brand)
    {
        //
        return view('admin.brand.show',compact('brand'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Brand $brand)
    {
        //
        return view('admin.brand.edit',compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Brand $brand)
    {
        //
        $this->validate($request,[
            'name_ar'=>'required',
            'name_en'=>'required'
            ],[
                'name_ar.required'=>'يجب ادخال الاسم بالعربية',
                'name_en.required'=>'يجب ادخال الاسم بالانجليزية'
            ]);
        $brand->name_ar = $request->name_ar;
        $brand->name_en = $request->name_en;
        if ($request->hasFile('img')) {
            \File::delete('admin/brands/'.$brand->img);
            $picName = rand(11111,99999).'_'.$request->file('img')->getClientOriginalName();
            $request->file('img')->move('admin/brands', $picName);
            $brand->img = $picName;
        }
        $brand->save();
        $request->session()->flash('success','تم تعديل الماركة بنجاح');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Brand $brand)
    {
        //
        $brand->delete();
        return back();

    }
}
