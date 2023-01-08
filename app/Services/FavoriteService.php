<?php

namespace App\Services;

use App\Models\Favorite;
use App\Repositories\FavoriteRepository;
use Auth;
use Illuminate\Http\Request;

class FavoriteService
{
    protected $favorite;

    public function __construct(Favorite $favorite)
    {
        $this->favorite = new FavoriteRepository($favorite);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->favorite->index();
    }

    /**
     * favorite a newly created resource in storage.
     */
    public function store($request)
    {
        return $this->favorite->store($request);
    }
    /**
     * Add product to favorite list.
     *
     */
    public function addProduct($request)
    {
        return $this->favorite->addProduct($request);
    }
    /**
     * Delete product from favorite list.
     *
     */
    public function deleteProduct($request)
    {
        return $this->favorite->deleteProduct($request);
    }
    /**
     * favorite products list.
     */
    public function fsavoriteProducts()
    {
        return $this->favorite->fsavoriteProducts();

    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return $this->favorite->show($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($request, $id)
    {
        return $this->favorite->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return $this->favorite->destroy($id);
    }

    /**
     * Refavorite the specified resource from storage.
     */
    public function refavorite($id)
    {
        return $this->favorite->refavorite($id);
    }
    /**
     * Get All favorites with the favorite of the week.
   
     */
    public function favoriteOfTheWeek($id)
    {
        return $this->favorite->favoriteOfTheWeek($id);
    }
    /**
     *7 New favorites
     *7 Featured products    
     *favorite of the week with 7 of its products
     */
    public function newOfTheWeek($id)
    {
        return $this->favorite->newOfTheWeek($id);
    }
    /**
     *Get the selected favorite with thier available genders
     */
    public function withGenders($favorite_id)
    {
        return $this->favorite->withGenders($favorite_id);
    }

    /**
     *    Follow a favorite and add it to the users followed favorites
     */
    public function follow($favorite_id)
    {
        return $this->favorite->follow($favorite_id);
    }

    /**
     *Get favorites categroies API
     */
    public function categroies($favorite_id, $gender_id)
    {
        return $this->favorite->categroies($favorite_id, $gender_id);
    }
    /**
     *Get category's sub categories of a favorite API
     */
    public function subcategories($favorite_id, $gender_id, $category_id)
    {
        return $this->favorite->subcategories($favorite_id, $gender_id, $category_id);
    }
    /**
     *Get category's products of a favorite API
     */
    public function products($favorite_id, $gender_id, $category_id)
    {
        return $this->favorite->products($favorite_id, $gender_id, $category_id);
    }
    /**
    //  *Get all products available colors to filter with
    //  */
    public function categoryColor($category_id)
    {
        return $this->favorite->categoryColor($category_id);
    }
    /**
    //  *Get all products available sizes to filter with
    //  */
    public function categorySizes($category_id)
    {
        return $this->favorite->categorySizes($category_id);
    }
    /**
    //  *Get all system categories of a specific gender API
    //  */
    public function GenderCategories($city_id, $gender_id)

    {
        return $this->favorite->GenderCategories($city_id, $gender_id);
    }


    /**
    //  *get all main categories for all favorites by city id
    //  */
    public function cityfavoritesCtegories($city_id)
    {
        return $this->favorite->cityfavoritesCtegories($city_id);
    }

    /**
    //  *get categories for all favorites by city id and gender id
    //  */
    public function genderCategoryProducts( $gender_id, $category_id)
    {
        return $this->favorite->genderCategoryProducts( $gender_id, $category_id);
    }


    /**
    //  *Get category's subcategories of specific gender API
    //  */
    public function genderCategoriesSubcategories($gender_id, $category_id)
    {
        return $this->favorite->genderCategoriesSubcategories($gender_id, $category_id);

    }

}
