<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\PriceRangeRequest;
use App\Models\PriceRange;
use Illuminate\Http\Request;

class PriceRangesController extends Controller
{
    public function index()
    {
        $price_range = PriceRange::paginate(10);
        return response()->json([
            "status" => 200,
            'data' => $price_range,
        ]);
    }
    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\Response
     */

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PriceRangeRequest $request)
    {
        ///---
        $price_range = PriceRange::create([
            'price_from' => $request->price_from,
            'price_to' => $request->price_to,
            'selling_price' => $request->selling_price,
        ]);
        return response()->json([
            "message" => "تمت إضافة التصنيف بنجاح",
            "status" => 201,
            'data' => $price_range,
        ]);
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function getStaticPrice(Request $request)
    {

        $price_range = PriceRange::where('price_from', '<=', $request->price)
            ->where('price_to', '>=', $request->price)
            ->get();
        return response()->json([
            "message" => "تمت إضافة التصنيف بنجاح",
            "status" => 201,
            'data' => $price_range,
        ]);
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