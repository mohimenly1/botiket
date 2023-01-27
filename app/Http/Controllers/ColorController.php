<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Http\Requests\ColorRequest;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $color = Color::all();
        return response()->json([
            "status" => 200,
            'data' => $color,
        ]);
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
    public function store(ColorRequest $request)
    {



        $color = Color::create([
            'name' => $request->name,
            'color_value' => $request->color_value,

        ]);
        return response()->json([
            "message" => "تمت إضافة اللون بنجاح",
            "status" => 201,
            'data' => $color,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Color  $color
     * @return \Illuminate\Http\Response
     */
    public function show(Color $color)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Color  $color
     * @return \Illuminate\Http\Response
     */
    public function edit(Color $color)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Color  $color
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Color $color)
    {
        $item = Color::find($color);
        $data = $item->update([
            'name' => $request->name,
            'color_value' => $request->color_value,

        ]);
        return response()->json([
            "message" => "تمت إضافة اللون بنجاح",
            "status" => 201,
            'data' => $data,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Color  $color
     * @return \Illuminate\Http\Response
     */
    public function destroy(Color $id)
    {
        // $color = Color::find($id);
        // dd($color);
        $id->delete();
        return response()->json([
            "message" => "تمت حذف اللون بنجاح",
            "status" => 202,
            'data' => $id,
        ]);
    }
}