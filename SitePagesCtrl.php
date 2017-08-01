<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Reviews;
use App\Initiative;
class SitePagesCtrl extends Controller
{
    //
    public function home()
    {
         $popularIni = Reviews::select('initiatives_id',\DB::raw('sum(rating) as total_votes '))->groupBy('initiatives_id')->take(4)->orderBy('total_votes','DESC')
                     ->get();
        $popular  = [];
        $rate  = [];
        foreach ($popularIni as $value) {
            $popular[] =  Initiative::where('id',$value->initiatives_id)->first();
            $rate[] = (int) $this->rate($value->initiatives_id)['rate'];
        }
        return view('site.pages.home',compact('popular','rate'));    
    }

    public function rate($id)
    {
        $reviews_5 = Reviews::where('initiatives_id',$id)->where('rating',5)->count();
        $reviews_4 = Reviews::where('initiatives_id',$id)->where('rating',4)->count();
        $reviews_3 = Reviews::where('initiatives_id',$id)->where('rating',3)->count();
        $reviews_2 = Reviews::where('initiatives_id',$id)->where('rating',2)->count();
        $reviews_1 = Reviews::where('initiatives_id',$id)->where('rating',1)->count();

        $div = ($reviews_5 + $reviews_4 + $reviews_3 + $reviews_2 + $reviews_1);
        $rate = 0;
        if ($div != 0) {
            $rate = ($reviews_5 * 5 + $reviews_4 * 4 + $reviews_3 * 3 + $reviews_2 * 2 + $reviews_1 * 1) / $div ;
        }
        $data = ['rate'=> $rate,'count'=> $div];
        return $data;
    }

    public function whoWe()
    {
        return view('site.pages.whoWe');    
    }

    public function goals()
    {
        return view('site.pages.goals');    
    }

    public function associations()
    {
        return view('site.pages.associations');    
    }

    public function businesses()
    {
        return view('site.pages.businesses');    
    }

    public function media()
    {
        return view('site.pages.media');    
    }

    public function partner()
    {
        return view('site.pages.partner');    
    }
    
    public function contact()
    {
        return view('site.pages.contact');  
    }

    public function contactPost(Request $request)
    {
        
        $this->validate($request,[
            'name'=>'required',
            'subject'=>'required',
            'email'=>'required',
            'message'=>'required',
        ],[
            'name.required'=>'يجب ادخال الاسم',
            'subject.required'=>'يجب ادخال الموضوع',
            'email.required'=>'يجب ادخال البريد الالكتروني',
            'message.required'=>'يجب ادخال الرسالة',
        ]);

        \Mail::send('email.contact', ['data'=>$request->all()],  function ($m) use($request)  {
            $m->from($request->email, $request->name);

            $m->to('info@sahem-csr.com','Sahem')->subject($request->subject);
        });
        $request->session()->flash('success','تم الارسال بنجاح');
        return back();
    }

    public function news($id)
    {
        $news = \App\News::find($id);
        // return $news;
        return view('site.pages.news',compact('news'));
    }

    public function searchHome(Request  $request)
    {
        $inputSearch = trim( $request->inputSearch," ");
        $initiative = Initiative::where('title', 'like', '%'.$inputSearch.'%')->get();
        $partner = \App\Partners::where('name', 'like', '%'.$inputSearch.'%')->get();
        // $partner = \App\Partners::all();
        $charities = \App\User::where('name', 'like', '%'.$inputSearch.'%')->get();
        return view('site.browse.searchHome',compact('initiative','charities','partner') );
    }

    public function log()
    {
       return view('site.pages.log');
    }

}
