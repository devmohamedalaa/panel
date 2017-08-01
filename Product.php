<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //

    public function images()
    {
        return $this->hasMany('App\ProductImg')->select('id','img')->orderBy('id','asc')->get();
    }

    public static $rules = [
    	'name_ar.required'=>'يجب ادخال اسم المنتج بالعربية',
        'name_en.required'=>'يجب ادخال اسم المنتج بالانجليزية',
        
        'content_ar.required'=>'يجب ادخال محتوي المنتج بالعربية',
        'content_en.required'=>'يجب ادخال محتوي المنتج بالانجليزية',

        'product_id.required'=>'يجب ادخال رقم المنتج ',
        'product_id.integer'=>'يجب ان يكون رقم المنتج عدد صحيح ',

        'price.required'=>'يجب ادخال سعر المنتج ',

        'category_id.integer'=>'يجب ان يكون رقم القسم عدد صحيح ',
        'category_id.required'=>'يجب ادخال القسم ',

        'product_id.integer'=>'يجب ان يكون رقم الكمية عدد صحيح ',
        'product_id.unique'=>'رقم المنتج موجود من قبل',
        'size.required'=>'يجب اختيار مقاس واحد علي الاقل',


        'slug_ar.unique'=>'permalink ar موجود من قبل',
        'slug_en.unique'=>'permalink en المنتج موجود من قبل',

        'meta_description_ar.required'=>'يجب ادخال Meta Description بالعربية',
        'meta_description_en.required'=>'يجب ادخال  Meta Description بالانجليزية',

        'meta_keywords_ar.required'=>'يجب ادخال Meta Tag بالعربية',
        'meta_keywords_en.required'=>'يجب ادخال Meta Tag بالانجليزية',

        'img.required'=>'يجب ادخال الصورة الرئيسية',
        'img.mimes'=>'انواع الصور المسموح بها فقط jpeg,png,bmp',

        'images.*.mimes'=>'انواع الصور المسموح بها فقط jpeg,png,bmp',

    ];

    public function getRouteKeyName()
    {
        return 'slug_ar';
    }

    public function setSizeAttribute($value)
    {
        $this->attributes['size'] = implode("|",$value);
    }

    public function getSizeAttribute($value)
    {
        return explode("|",$value);
    }
}
