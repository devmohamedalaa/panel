<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Initiative;
class InitiativeCtrl extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($size)
    {
        //
        $initiative = Initiative::orderBY('id','desc')->where('size',$size)->get();
        return view('admin.initiative.showSize',compact('initiative'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editSize(Request $request)
    {
        //
        foreach ($request->initiative as $index => $value) {
            $initiative = Initiative::find($value);
            $initiative->num = $request->num[$index];
            $initiative->save();
        }
        return back();
    }

    public function deleteSize(Initiative $initiative)
    {
        // return $initiative;
        //
        $offers = \App\Offers::where('initiative_id',$initiative->id)->get();
        foreach ($offers as $offer) {
            $offer->delete();
        }
        $files = explode('|', $initiative->files);
        foreach ($files as $file) {
            \File::delete('admin/images/initiative/'.$file);
        }
        \File::delete('admin/images/initiative/'.$initiative->img);
        \File::delete('admin/images/initiative/'.$initiative->icon);
        $initiative->delete();
        return back();
    }

    /**
     * Upbeneficial the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Initiative $initiative)
    {
        //
        // return $request->all();
        $this->validate($request,[
            // 'icon'=>'required',
            'title'=>'required',
            'desc_problem'=>'required',
            'desc_initiative'=>'required',
            'price'=>'required',
            'count'=>'required',
            'beneficial'=>'required',
            'time'=>'required',
            'city'=>'required',
            'objectives'=>'required',
            'riskiness'=>'required',
            'relatedـparties'=>'required',
            // 'img'=>'required',
            'success'=>'required'
        ],Initiative::$ruels);

        // $initiative = new Initiative;
         if(empty($request->charity_id)) { 
            $charity_id =  auth()->user()->id ;
        }else{  
            $charity_id =  $request->charity_id ;
        } 
        $initiative->charity_id = $charity_id ;
        $initiative->title = $request->title;
        $initiative->desc_problem = $request->desc_problem;
        $initiative->desc_initiative = $request->desc_initiative;
        $initiative->price = $request->price;
        $initiative->count = $request->count;
        $initiative->beneficial = $request->beneficial;
        $initiative->time = $request->time;
        $initiative->city = $request->city;
        $initiative->objectives = $request->objectives;
        $initiative->riskiness = $request->riskiness;
        $initiative->relatedـparties = $request->relatedـparties;
        $initiative->success = $request->success;
        $initiative->type_id = $request->type_id;

        if ($request->file('files')) {
            foreach ($request->file('files') as $file) {
                $fileName = rand(11111,99999).'_'.$file->getClientOriginalName();
                $file->move('admin/images/initiative', $fileName);
                $files[] =$fileName;
            }
            $initiative->files = implode('|', $files);
        }
        if ($request->hasFile('img')) {
            $picName = rand(11111,99999).'_'.$request->file('img')->getClientOriginalName();
            $request->file('img')->move('admin/images/initiative',$picName);
            \File::delete('admin/images/initiative/'.$initiative->img);
            $initiative->img = $picName;
        }
        if ($request->hasFile('icon')) {
            $iconName = rand(11111,99999).'_'.$request->file('icon')->getClientOriginalName();
            $request->file('icon')->move('admin/images/initiative',$iconName);
            \File::delete('admin/images/initiative/'.$initiative->icon);
            $initiative->icon = $iconName;
        }
        $initiative->link = $request->link;
        $initiative->size = $request->size;
        $initiative->save();
        $request->session()->flash('success','تم التعديل المبادرة بنجاح');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Initiative $initiative)
    {
        //
        // return $initiative;
        $offers = \App\Offers::where('initiative_id',$initiative->id)->get();
        foreach ($offers as $offer) {
            $offer->delete();
        }
        $files = explode('|', $initiative->files);
        foreach ($files as $file) {
            \File::delete('admin/images/initiative/'.$file);
        }
        \File::delete('admin/images/initiative/'.$initiative->img);
        \File::delete('admin/images/initiative/'.$initiative->icon);
        $initiative->delete();
        return back();
    }

}
