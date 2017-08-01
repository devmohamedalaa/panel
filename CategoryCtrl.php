<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Category;
class CategoryCtrl extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $category = Category::all();
        return view('admin.category.index',compact('category'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.category.create');
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
            'name_en'=>'required'
            ],[
                'name_ar.required'=>'يجب ادخال الاسم بالعربية',
                'name_en.required'=>'يجب ادخال الاسم بالانجليزية'
            ]);
        $category = new Category();
        $category->name_ar = $request->name_ar;
        $category->name_en = $request->name_en;
        $category->save();
        $request->session()->flash('success','تمت اضافة قسم بنجاح');
        return redirect('admin/category');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
        return view('admin.category.show',compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        //
        return view('admin.category.edit',compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        //
        $this->validate($request,[
            'name_ar'=>'required',
            'name_en'=>'required'
            ],[
                'name_ar.required'=>'يجب ادخال الاسم بالعربية',
                'name_en.required'=>'يجب ادخال الاسم بالانجليزية'
            ]);
        $category->name_ar = $request->name_ar;
        $category->name_en = $request->name_en;
        $category->save();
        $request->session()->flash('success','تم التعديل القسم بنجاح');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        //
        $category->delete();
        return back();

    }
}
