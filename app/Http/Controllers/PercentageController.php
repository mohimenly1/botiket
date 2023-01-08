<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewPercentage;
use App\Models\Category;
use App\Models\Percentage;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseTraits;
use App\Models\Brand;

class PercentageController extends Controller
{
    use ResponseTraits;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($request)
    {
        $data = Percentage::all();
        return $this->prepare_response(null, "", $data, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(NewPercentage $request)
    {
        try {

            $input = $request->validated();
            if ( $request->has("sub_category_id") ) {
                Percentage::create($input);
            } else {
                

                $category = Category::find($input['category_id']);
                foreach ($category->subCategories as $subCategory) {

                    $percentage = Percentage::where([
                        "category_id" => $category->id,
                        "sub_category_id" => $subCategory->id,
                    ])->first();

                    if( !isset($percentage)){
                        $input['sub_category_id'] = $subCategory->id;
                        Percentage::create($input);
                    }
                }
            }
            return $this->prepare_response(null, "تمت العملية بنجاح", null, 200);
        } catch (\Throwable $e) {
            return $this->prepare_response($e->getMessage(), "something went wrong", null, 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($request, Percentage $percentage)
    {
        try {
            return $this->prepare_response(null, "تمت العملية بنجاح", $percentage, 200);
        } catch (\Throwable $e) {
            return $this->prepare_response($e->getMessage(), "something went wrong", null, 400);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(NewPercentage $request, Brand $brand,Percentage $percentage)
    {
        try {
            $input = $request->validated();
            $percentage->update($input);
            $percentage->refresh();

            return $this->prepare_response(null, "تمت العملية بنجاح", $percentage, 200);
        } catch (\Throwable $e) {
            return $this->prepare_response($e->getMessage(), "something went wrong", null, 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($request,Brand $brand, Percentage $percentage)
    {
        try {
            $percentage->delete();
            return $this->prepare_response(null, "تمت العملية بنجاح", $percentage, 200);
        } catch (\Throwable $e) {
            return $this->prepare_response($e->getMessage(), "something went wrong", null, 400);
        }
    }
}
