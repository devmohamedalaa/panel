<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Image;
class ImageCtrl extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $image = Image::orderBy('id','desc')->get();
        return view('admin.image.index',compact('image'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.image.create');

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
        $this->validate($request,['title'=>'required','image.*'=>'required'],Image::$ruels);
        $image = new Image();
        $image->title = $request->title;

        if ($request->file('image')) {
            // $images = "";
            foreach ($request->file('image') as $file) {
                $imgName = rand(11111,99999).'_'.$file->getClientOriginalName();
                $file->move('admin/image', $imgName);
                $images[] =$imgName;
            }
        }

        $image->image = implode('|', $images);
        $image->save();
        return redirect('admin/images')->with('success','تمت الاضافة بنجاح'); 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Image $images)
    {
        return view('admin.image.show',compact('images'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Image $images)
    {
        //
        return view('admin.image.edit',compact('images'));

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

        $this->validate($request,['title'=>'required'],Image::$ruels);
        $images = Image::find($id);
        $images->title = $request->title;
        if ($request->file('image')) {

            foreach ($request->file('image') as $file) {
                if(!empty($file)){
                    $imgName = rand(11111,99999).'_'.$file->getClientOriginalName();
                    $file->move('admin/image', $imgName);
                    $img[] =$imgName;
                }else{
                    $img = [];
                }

            }
            $images->image = $images->image.'|'.implode('|', $img);

        }
        $images->save();
        return back()->with('success','تم التعديل بنجاح');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Image $images)
    {
        //
        $image = explode('|', $images->image);
        foreach ($image as  $value) {
            \File::delete('admin/image/'.$value);
        }
            
        $images->delete();
        return back();
    }

    public function deleteImg($nameImg,$id){
        $images = Image::find($id);
        $image = explode('|', $images->image);
        if(($key = array_search($nameImg, $image)) !== false) {
            unset($image[$key]);
            \File::delete('admin/image/'.$image[$key]);
        }
        $images->image = implode("|",$image);
        // unlink(public_path('file/to/delete'));
        $images->save();
        return back();
    }   
}
