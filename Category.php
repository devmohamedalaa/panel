<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    public function product()
    {
    	return $this->hasOne('App\Product')->select('name_ar','name_en','img','price')->limit(4)->orderBy('id','asc')->get();
    }
}
