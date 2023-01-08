<?php

namespace App\Services;

use App\Models\Store;
use App\Repositories\StoreRepository;
use Auth;
use Illuminate\Http\Request;

class StoreService
{
    protected $store;

    public function __construct(Store $store)
    {
        $this->store = new StoreRepository($store);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->store->index();
    }
    /**
     * Display a listing of the resource.
     */
    public function indexDeleted()
    {
        return $this->store->indexDeleted();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($request)
    {
        return $this->store->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return $this->store->show($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($request, $id)
    {
        return $this->store->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return $this->store->destroy($id);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        return $this->store->restore($id);
    }
    /**
     * Get All Stores with the store of the week.

     */
    public function StoreOfTheWeek($id)
    {
        return $this->store->StoreOfTheWeek($id);
    }
    /**
     *7 New stores
     *7 Featured products
     *Store of the week with 7 of its products
     */
    public function newOfTheWeek()
    {
        return $this->store->newOfTheWeek();
    }
    /**
     *The search will be at the level of the products and stores table
     */
    public function mainSearch($search)
    {
        return $this->store->mainSearch($search);
    }

    /**
     *Get the selected store with thier available genders
     */
    public function withGenders($store_id)
    {
        return $this->store->withGenders($store_id);
    }

    /**
     *    Follow a store and add it to the users followed stores
     */
    public function follow($store_id)
    {
        return $this->store->follow($store_id);
    }

    /**
     *Get stores categroies API
     */
    public function categroies($store_id, $gender_id)
    {
        return $this->store->categroies($store_id, $gender_id);
    }
    /**
     *Get category's sub categories of a store API
     */
    public function subcategories($store_id, $gender_id, $category_id)
    {
        return $this->store->subcategories($store_id, $gender_id, $category_id);
    }
    /**
     *Get category's products of a store API
     */
    public function products($store_id, $gender_id, $category_id)
    {
        return $this->store->products($store_id, $gender_id, $category_id);
    }
    /**
    //  *Get all products available colors to filter with
    //  */
    public function categoryColor($category_id)
    {
        return $this->store->categoryColor($category_id);
    }
    /**
    //  *Get all products available sizes to filter with
    //  */
    public function categorySizes($category_id)
    {
        return $this->store->categorySizes($category_id);
    }

    /**
    //  *Get all products available colors to filter with
    //  */
    public function categoryStoreColors($category_id, $store_id)
    {
        return $this->store->categoryStoreColors($category_id, $store_id);
    }
    /**
    //  *Get all products available sizes to filter with
    //  */
    public function categoryStoreSizes($category_id, $store_id)
    {
        return $this->store->categoryStoreSizes($category_id, $store_id);
    }
    /**
    //  *Get all system categories of a specific gender API
    //  */
    public function GenderCategories($city_id, $gender_id)

    {
        return $this->store->GenderCategories($city_id, $gender_id);
    }
      /**
    //  *Get all system categories of a specific gender API
    //  */
    public function brandGendersCategories($brand_id)

    {
        return $this->store-> brandGendersCategories($brand_id);
    }

      /**
    //  *Get ALL system categories for a gender
    //  */
    public function AllGenderCategories($gender_id)

    {
        return $this->store-> AllGenderCategories($gender_id);
    }


    /**
    //  *get all main categories for all stores by city id
    //  */
    public function cityStoresCtegories($city_id)
    {
        return $this->store->cityStoresCtegories($city_id);
    }

    /**
    //  *get categories for all stores by city id and gender id
    //  */
    public function genderCategoryProducts($city_id, $gender_id, $category_id)
    {
        return $this->store->genderCategoryProducts($city_id, $gender_id, $category_id);
    }


    /**
    //  *Get category's subcategories of specific gender API
    //  */
    public function genderCategoriesSubcategories($city_id, $gender_id, $category_id)
    {
        return $this->store->genderCategoriesSubcategories($city_id, $gender_id, $category_id);

    }
      /**
    //  *Get category's subcategories of specific gender API
    //  */
    public function brandCategoryProducts($brand_id, $category_id)
    {
        return $this->store->brandCategoryProducts($brand_id, $category_id);

    }
      /**
    //  *Get all system products of a specific gender API
    === create date: Jan-10th-2022
    //  */
    public function brandGenderProducts($brand_id, $gender_id)
    {
        return $this->store->brandGenderProducts($brand_id, $gender_id);

    }

     /**
    //  *Get all products from all brands based on category and gender
    === create date: Feb-5th-2022
    //  */
    public function brandProductsGenderCategories($category_id)
    {
        return $this->store->brandProductsGenderCategories( $category_id);

    }


     /**
    //  *Get all products from all brands based on category and gender
    === create date: Feb-9th-2022
    //      */
    public function colorsSizesForCategory($category_id)
    {
        return $this->store->colorsSizesForCategory( $category_id);

    }


    /**
    //  *Filter all products from all brands based on category, color and size
    === create date: Feb-10th-2022
    //  */
   public function filterColorsSizesForCategory($request)
   {
       return $this->store->filterColorsSizesForCategory($request);

   }
    /**
     *Get all products available colors to filter with
     */
    public function brandColors($brand_id,$category_id)
    {
        return $this->store->brandColors($brand_id,$category_id);

    }
    /**
     *Get all products available sizes to filter with
     */
    public function brandSizes($brand_id,$category_id)
    {
        return $this->store->brandSizes($brand_id,$category_id);

    }

}
