<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\HighlightRequest;
use App\Models\Highlight;
use Illuminate\Http\Request;

class HighlightController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $data = Highlight::with('product')->paginate(10);
        return response()->json([
            "status" => 200,
            'data' => $data,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(HighlightRequest $request)
    {
        $price_range = Highlight::create([

            'product_id' => $request->product_id,
            'note'       => $request->note
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function delete($id)
    {
        $Highlight = Highlight::find($id);
        $Highlight->delete();
    }

    public function restore($id)
    {
        $Highlight = Highlight::onlyTrashed()->find($id);
        $Highlight->restore();
    }
    public function destroy($id)
    {
        //
    }
}