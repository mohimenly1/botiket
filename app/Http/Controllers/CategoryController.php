<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Gender;
use Illuminate\Http\Request;
use App\Http\Requests\CategoryRequest;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::with('subCategories')->filter(request()->get('search'))->paginate(10);
        return response()->json([
            "status" => 200,
            'data' => $categories,
        ]);
    }
    /**
     * Show the form for creating a new resource.
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
    public function store(CategoryRequest $request)
    {
        // $request->validate([
        //     'name' => 'required||unique:categories',
        //     'image' => 'required',
        //     'gender_id' => 'required',
        // ]);
        $gender = Gender::find($request->gender_id);
        ///

        // $image_path = $request->image->store('/categories', 'public');
        // Storage::disk('public')->setVisibility($image_path, 'public');
        // $image = Storage::disk('public')->url($image_path);


        $image = $request->image;
        $imageName = $request->name . $gender->name . "-" . rand(1000, 2000) . '.jpg';
        $image->move(public_path('images/categories'), $imageName);
        $request->image = '/images/categories/' . $imageName;
        //////---
        // $second_image_path = $request->second_image->store('/categories', 'public');
        // Storage::disk('public')->setVisibility($second_image_path, 'public');
        // $second_image = Storage::disk('public')->url($second_image_path);
        //dd($image);
        $second_image = $request->second_image;
        $imageName_second_image = $request->name . $gender->name . "-" . rand(3000, 4000) . '.jpg';
        $second_image->move(public_path('images/categories'), $imageName_second_image);
        $request->second_image = '/images/categories/' . $imageName_second_image;
        /////---
        $category = Category::create([
            'name' => $request->name,
            'image' => $request->image,
            'second_image' => $request->second_image,
            'gender_id' => $request->gender_id,
        ]);
        return response()->json([
            "message" => "تمت إضافة التصنيف بنجاح",
            "status" => 201,
            'data' => $category,
        ]);
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = Category::find($id);
        return response()->json($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $gender = Gender::find($request->gender_id);
        if (request()->has('image')) {
            $image = $request->image;
            $imageName = $request->name . $gender->name . '.jpg';
            $image->move(public_path('images/categories'), $imageName);
            $request->image = '/images/categories/' . $imageName;
            $category = Category::find($request->id);
            $category->update([
                'name' => $request->name,
                'image' => $request->image,
                'gender_id' => $request->gender_id,
            ]);
        } else {
            $category = Category::find($request->id);
            $category->update(
                $request->all()
            );
        }
        return response()->json([
            "message" => "تمت تعديل التصنيف بنجاح",
            "status" => 202,
            'data' => $category,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        $category->delete();
        return response()->json([
            "message" => "تمت حذف التصنيف بنجاح",
            "status" => 202,
            'data' => $category,
        ]);
    }
}