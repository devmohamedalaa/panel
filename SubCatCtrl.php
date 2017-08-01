<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Category;
use App\SubCat;
class SubCatCtrl extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        //
        $category = Category::select('id','name_ar')->findOrFail($id);
        $subCat = SubCat::select('id','name_ar')->where('cat_id',$id)->get();
        return view('admin.category.subCat.index',compact('category','subCat'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        //
        $category = Category::select('id','name_ar')->findOrFail($id);
        return view('admin.category.subCat.create',compact('category','id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$cat_id)
    {
        //
        $this->validate($request,[
            'name_ar'=>'required',
            'name_en'=>'required',
            'cat_id'=>'requried',
        ],[
            'name_ar.required'=>'يجب ادخال الاسم بالعربية',
            'name_en.required'=>'يجب ادخال الاسم بالانجليزية',
            'cat_id.required'=>'يجب اختيار القسم الرئيسي'
        ]);

        $subCat = new SubCat();
        $subCat->name_ar = $request->name_ar;
        $subCat->name_en = $request->name_en;
        $subCat->cat_id  = $cat_id;
        $subCat->save();
        $request->session()->flash('success','تمت اضافة قسم بنجاح');
        return redirect('admin/subCat/'.$cat_id);

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
    }
}
