<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Slider;
class SliderCtrl extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $sliders = Slider::orderBy('id','desc')->get();
        return view('admin.slider.index',compact('sliders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.slider.create');
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
        $this->validate($request,['img'=>'required'],['img.required'=>'يجب ادخال الصورة']);
        $slider = new Slider();
        $imgName = rand(11111,99999).'_'.$request->img->getClientOriginalName();
        $request->img->move('admin/slider', $imgName);
        $slider->img = $imgName;

        $slider->title_ar = $request->title_ar;
        $slider->title_en = $request->title_en;
        
        $slider->desc_ar = $request->desc_ar;
        $slider->desc_en = $request->desc_en;

        $slider->save();
        return redirect('admin/sliders')->with('success','تمت الاضافة بنجاح');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Slider $sliders)
    {
        //
        return view('admin.slider.show',compact('sliders'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Slider $sliders)
    {
        //
        return view('admin.slider.edit',compact('sliders'));
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
        // dd($request->all());
        $sliders = Slider::find($id);
        if ($request->hasFile('img')) {
            \File::delete('admin/slider/'.$sliders->img);
            $imgName = rand(11111,99999).'_'.$request->img->getClientOriginalName();
            $request->img->move('admin/slider', $imgName);
            $sliders->img = $imgName;
            // return 'fds';
        }else{
            $sliders->img = $sliders->img;
        }

        $sliders->title_ar = $request->title_ar;
        $sliders->title_en = $request->title_en;
        
        $sliders->desc_ar = $request->desc_ar;
        $sliders->desc_en = $request->desc_en;

        $sliders->save();
        return back()->with('success','تم التعديل بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Slider $sliders)
    {
        //
        \File::delete('admin/slider/'.$sliders->img);
        $sliders->delete();
        return back()->with('success','تم الحذف بنجاح');;
    }
}
