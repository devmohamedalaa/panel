<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Video;
class VideoCtrl extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $videos = Video::orderBy('id','desc')->get();
        return view('admin.video.index',compact('videos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.video.create');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,['title'=>'required','video.*'=>'required|file'],Video::$ruels);
        $video = new Video();
        $video->title = $request->title;

        if ($request->file('video')) {
            foreach ($request->file('video') as $file) {
                $vidName = rand(11111,99999).'_'.$file->getClientOriginalName();
                $file->move('admin/video', $vidName);
                $videos[] =$vidName;
            }
        }

        $video->video = implode('|', $videos);
        $video->save();
        return redirect('admin/videos')->with('success','تمت الاضافة بنجاح'); 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Video $videos)
    {
        return view('admin.video.show',compact('videos'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Video $videos)
    {
        //
        return view('admin.video.edit',compact('videos'));

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
        $this->validate($request,['title'=>'required'],Video::$ruels);
        $videos = Video::find($id);
        $videos->title = $request->title;
        if ($request->file('video')) {

            foreach ($request->file('video') as $file) {
                if(!empty($file)){
                    $vidName = rand(11111,99999).'_'.$file->getClientOriginalName();
                    $file->move('admin/video', $vidName);
                    $vid[] =$vidName;
                }else{
                    $vid = [];
                }

            }
            $videos->video = $videos->video.'|'.implode('|', $vid);

        }
        $videos->save();
        return back()->with('success','تم التعديل بنجاح');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Video $videos)
    {
        //
        $videos->delete();
        return back();
    }

    public function deleteVideo($nameVid,$id){
        $videos = Video::find($id);
        $video = explode('|', $videos->video);
        if(($key = array_search($nameVid, $video)) !== false) {
            unset($video[$key]);
        }
        $videos->video = implode("|",$video);
        $videos->save();
        return back();
    }   
}
