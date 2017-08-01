<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Product;
use App\Brand;
use App\Category;
use App\SubCat;
use App\Type;
use App\ProductImg;
class ProductCtrl extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $products = Product::orderBy('id','desc')->get();

        return view('admin.product.index',compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $brands = Brand::all(); 
        $categorys = Category::all(); 
        $subCat = SubCat::all(); 
        // $types = Type::all(); 
        // return $subCat;
        return view('admin.product.create',compact('products','brands','categorys','types','subCat'));
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

            'images.*'=>'mimes:jpeg,jpg,png',
            'img'=>'required|mimes:jpeg,jpg,png',
            'price'=>'required',

            'product_id'=>'required|integer|unique:products',
            'brand_id'=>'required|integer',
            'category_id'=>'required|integer',

            'meta_description_ar'=>'required',
            'meta_description_en'=>'required',
            'size'=>'required',

            'meta_keywords_ar'=>'required',
            'meta_keywords_en'=>'required',

            'slug_ar'=>'unique:products',
            'slug_en'=>'unique:products',

        ],Product::$rules);
        $product = new Product();

        $product->name_ar = $request->name_ar;
        $product->name_en = $request->name_en;

        $product->product_id = $request->product_id;

        $product->type_id = $request->type_id;
        $product->category_id = $request->category_id;
        $product->sub_cat_id = $request->sub_cat_id;

        $product->price = $request->price;
        $product->brand_id = $request->brand_id;

        $product->availability_ar = $request->availability_ar;
        $product->availability_en = $request->availability_en;

        $product->meta_description_ar = $request->meta_description_ar;
        $product->meta_description_en = $request->meta_description_en;

        $product->meta_keywords_ar = str_replace(' ', ',',$request->meta_keywords_ar) ;
        $product->meta_keywords_en = str_replace(' ', ',',$request->meta_keywords_en) ;

        $product->slug_ar = str_replace(' ', '-',$request->name_ar);
        $product->slug_en = str_replace(' ', '-',$request->name_en);

        $product->size = $request->size;

        if ($request->file('img')) {
            $imgName = rand(11111,99999).'_'.$request->img->getClientOriginalName();
            $request->img->move('admin/products', $imgName);
            $product->img  = $imgName;

        }
        $product->save();

        if ($request->file('images')) {
            foreach ($request->file('images') as $file) {
                $img =  new ProductImg();
                $imgName = rand(11111,99999).'_'.$file->getClientOriginalName();
                $file->move('admin/products/img', $imgName);
                $img->product_id  = $product->id;
                $img->img  = imgName;
                $img->save();
            }
        }

        return redirect('admin/pro')->with('success','تمت الاضافة بنجاح');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Product $pro)
    {
        //
        // return $pro->size;
        $brands = Brand::all(); 
        $categorys = Category::all(); 
        $types = Type::all(); 
        return view('admin.product.show',compact('pro','brands','categorys','types'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $pro)
    {
        //
        $brands = Brand::select('id','name_ar')->get(); 
        $categorys = Category::select('id','name_ar')->get(); 
        $types = Type::select('id','name_ar')->get(); 
        return view('admin.product.edit',compact('pro','brands','categorys','types'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $pro)
    {
        //
        // dd($request->all());
        $this->validate($request,[
            'name_ar'=>'required',
            'name_en'=>'required',

            'price'=>'required',
            'product_id'=>'required|integer',

            'brand_id'=>'required|integer',
            'category_id'=>'required|integer',

            'meta_description_ar'=>'required',
            'meta_description_en'=>'required',
            'size'=>'required',

            'meta_keywords_ar'=>'required',
            'meta_keywords_en'=>'required',

        ],Product::$rules);

        $pro->name_ar = $request->name_ar;
        $pro->name_en = $request->name_en;

        $pro->product_id = $request->product_id;
        
        $pro->type_id = $request->type_id;
        $pro->category_id = $request->category_id;
        $pro->sub_cat_id = $request->sub_cat_id;
        

        $pro->price = $request->price;
        $pro->brand_id = $request->brand_id;

        $pro->availability_ar = $request->availability_ar;
        $pro->availability_en = $request->availability_en;

        $pro->meta_description_ar = $request->meta_description_ar;
        $pro->meta_description_en = $request->meta_description_en;

        $pro->meta_keywords_ar = $request->meta_keywords_ar;
        $pro->meta_keywords_en = $request->meta_keywords_en;

        // dd( $request->all() );
        $pro->slug_ar = str_replace(' ', '-',$request->name_ar);
        $pro->slug_en = str_replace(' ', '-',$request->name_en);

        $pro->size = $request->size;

        if ($request->file('img')) {
            \File::delete('admin/products/'.$pro->img);
            $imgName = rand(11111,99999).'_'.$request->img->getClientOriginalName();
            $request->img->move('admin/products', $imgName);
            $pro->img  = $imgName;
        }

        if ($request->file('images')) {
            foreach ($request->file('images') as $file) {
                $img =  new ProductImg();
                $imgName = rand(11111,99999).'_'.$file->getClientOriginalName();
                $file->move('admin/products/img', $imgName);
                $img->product_id  = $pro->id;
                $img->img  = $imgName;
                $img->save();
            }
        }

        $pro->save();
        
        return redirect('admin/pro/'.$pro->slug_ar.'/edit')->with('success','تم التعديل بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $pro)
    {
        
        \File::delete('admin/products/'.$pro->img);

        $proImg = ProductImg::where('product_id',$pro->id)->get();
        foreach ($proImg as $img) {
            \File::delete('admin/products/img/'.$img->img);
            $img->delete();
        }

        $pro->delete();
        return redirect('admin/pro')->with('success','تمت الحذف بنجاح');
    }

    public function deleteImg($id)
    {
        $proImg = ProductImg::find($id);
        \File::delete('admin/products/img/'.$proImg->img);
        $proImg->delete();

        return back();
    }

    // Site Section

    public function product()
    {
        $products = Product::paginate(18);
        return view('site.product.allProduct',compact('products'));
    }

    public function productDetails(Product $product)
    {
       return view('site.product.details',compact('product'));
    }

    public function brands($id)
    {
        $products = Product::where('brand_id',$id)->paginate(18);
        return view('site.product.allProduct',compact('products'));
    }

    public function category($id)
    {
        $products = Product::where('category_id',$id)->paginate(18);
        return view('site.product.allProduct',compact('products'));
    }

    
}
