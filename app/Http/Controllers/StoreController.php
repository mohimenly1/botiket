<?php

namespace App\Http\Controllers;


use App\Http\Traits\ResponseTraits;
use App\Services\StoreService;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRequest;

class StoreController extends Controller
{
    use ResponseTraits;
    protected $store_service;
    public function __construct(StoreService $service)
    {
        $this->store_service = $service;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */




    //shows store state true or false

    public function ShowState()
    {

        try{
            $jsonString = file_get_contents(__DIR__.'/_store_state.json', true);
            $data = json_decode($jsonString, false);

              return $this->prepare_response(null, 'store status',$data->state, 200);

        }catch(\Exception $e){
            return $this->prepare_response([$e], 'store status',null, 400);

        }
    }

    public function UpdatState()
    {
        try{
            $jsonString = file_get_contents(__DIR__.'/_store_state.json', true);


            $data = json_decode($jsonString, false);
              $data->state = !$data->state;
              $newData = json_encode($data, JSON_PRETTY_PRINT);
              file_put_contents(__DIR__.'/_store_state.json', stripslashes($newData));

              return $this->prepare_response(null, 'store status',$data->state, 200);

        }catch(\Exception $e){
            return $this->prepare_response([$e], 'store status',null, 400);

        }


    }

    public function index()
    {
        try {
            $data = $this->store_service->index();
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexDeleted()
    {
        try {
            $data = $this->store_service->indexDeleted();
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        try {
            $data = $this->store_service->store($request);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $data = $this->store_service->show($id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreRequest $request, $id)
    {
        try {
            $data = $this->store_service->update($request, $id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $data = $this->store_service->destroy($id);
            return  $data;
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        try {
            $data = $this->store_service->restore($id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }


    /**************************    Application APIS  **********************************/

    /**
     * Get All Stores with the store of the week.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeOfTheWeek($id)
    {
        try {
            $data = $this->store_service->StoreOfTheWeek($id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    /**
     *7 New stores
     *7 Featured products
     *Store of the week with 7 of its products
     */
    public function newOfTheWeek()
    {
        try {
            $data = $this->store_service->newOfTheWeek();
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    /**
     *The search will be at the level of the products and stores table
     */
    public function mainSearch($search)
    {
        try {
            $data = $this->store_service->mainSearch($search);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     *Get the selected store with thier available genders
     */
    public function withGenders($store_id)
    {
        try {
            $data = $this->store_service->withGenders($store_id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     *    Follow a store and add it to the users followed stores
     */
    public function follow($store_id)
    {
        try {
            $data = $this->store_service->follow($store_id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     *Get stores categroies API
     */
    public function categroies($store_id, $gender_id)
    {
        // try {
        $data = $this->store_service->categroies($store_id, $gender_id);
        return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        // } catch (\Exception $e) {
        //     return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        // }
    }
    /**
     *Get category's sub categories of a store API
     */
    public function subcategories($store_id, $gender_id, $category_id)
    {
        try {
            $data = $this->store_service->subcategories($store_id, $gender_id, $category_id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    /**
     *Get category's products of a store API
     */
    public function products($store_id, $gender_id, $category_id)
    {
        try {
            $data = $this->store_service->products($store_id, $gender_id, $category_id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     *Get all products available colors to filter with
     */
    public function categoryColors($category_id)
    {
        try {
            $data = $this->store_service->categoryColor($category_id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    /**
    //  *Get all products available sizes to filter with
    //  */
    public function categorySizes($category_id)
    {
        try {
            $data = $this->store_service->categorySizes($category_id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
    //  *Get all products available colors to filter with
    //  */
    public function categoryStoreColors($category_id, $store_id)
    {
        try {
            $data = $this->store_service->categoryStoreColors($category_id, $store_id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    /**
    //  *Get all products available sizes to filter with
    //  */
    public function categoryStoreSizes($category_id, $store_id)
    {
        try {
            $data = $this->store_service->categoryStoreSizes($category_id, $store_id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
    //  *Get all system categories of a specific gender API
    //  */
    public function GenderCategories($city_id, $gender_id)
    {
        try {
            $data = $this->store_service->GenderCategories($city_id, $gender_id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    /**
    //  *Get all system categories of a specific gender API
    //  */
    public function brandGendersCategories($brand_id)
    {
        try {
            $data = $this->store_service->brandGendersCategories($brand_id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    /**
    //  *Get all system categories of a specific gender API
    //  */
    public function AllGenderCategories($gender_id)
    {
        try {
            $data = $this->store_service->AllGenderCategories($gender_id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    /**
    //  *Get all system categories of a specific gender API
    //  */
    public function brandCategoryProducts($brand_id, $category_id)
    {
        try {

            $data = $this->store_service->brandCategoryProducts($brand_id, $category_id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
    //  *Get all system products of a specific gender API
    === create date: Jan-10th-2022
    //  */
    public function brandGenderProducts($brand_id, $gender_id)
    {
        try {
            $data = $this->store_service->brandGenderProducts($brand_id, $gender_id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    /**
    //  *Get all products from all brands based on category and gender
    === create date: Feb-5th-2022
    //  */
    public function brandProductsGenderCategories($category_id)
    {
        try {
            $data = $this->store_service->brandProductsGenderCategories($category_id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
    //  *Get all products from all brands based on category and gender
    === create date: Feb-9th-2022
    //  */
    public function colorsSizesForCategory($category_id)
    {
        try {
            $data = $this->store_service->colorsSizesForCategory( $category_id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    /**
    //  *Filter all products from all brands based on category, color and size
    === create date: Feb-10th-2022
    //  */
    public function filterColorsSizesForCategory(Request $request)
    {
        try {
            $data = $this->store_service->filterColorsSizesForCategory($request);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }


    /**
    //  *get all main categories for all stores by city id
    //  */
    public function cityStoresCtegories($city_id)
    {
        try {
            $data = $this->store_service->cityStoresCtegories($city_id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
    //  *get categories for all stores by city id and gender id
    //  */
    public function genderCategoryProducts($city_id, $gender_id, $category_id)
    {
        try {

            $data = $this->store_service->genderCategoryProducts($city_id, $gender_id, $category_id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
    //  *Get category's subcategories of specific gender API
    //  */
    public function genderCategoriesSubcategories($city_id, $gender_id, $category_id)
    {
        try {

            $data = $this->store_service->genderCategoriesSubcategories($city_id, $gender_id, $category_id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }

    /**
     *Get all products available colors to filter with
     */
    public function brandColors($brand_id, $category_id)
    {
        try {
            $data = $this->store_service->brandColors($brand_id, $category_id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
    /**
     *Get all products available sizes to filter with
     */
    public function brandSizes($brand_id, $category_id)
    {
        try {
            $data = $this->store_service->brandSizes($brand_id, $category_id);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
    }
}
