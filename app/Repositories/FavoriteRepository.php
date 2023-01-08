<?php

namespace App\Repositories;

use App\Helpers\FileHelper;
use App\Http\Traits\CrudTrait;
use App\Http\Traits\MainTrait;
use App\Http\Traits\ResponseTraits;
use App\Models\BeamsNotification;
use App\Models\Category;
use App\Models\FavItem;
use App\Models\Product;
use App\Models\Favorite;
use App\Models\SubCategory;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;

class FavoriteRepository
{
    use CrudTrait, ResponseTraits, MainTrait;
    /**
     * @var Favorite
     */
    protected $favorite;

    /**
     * UserRepository constructor.
     *
     * @param Favorite $favorite
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
    /**
     * Get all Favorites with Role.
     *
     * @return Favorite $favorite
     */
    public function index()
    {
        $favorite = $this->model
            ->where('user_id', Auth::id())
            ->with(['items.product:id,title,sku,price,offer_id,created_at,updated_at', 'items.product.firstMedia'])
            ->get();


        return $favorite;
    }

    /**
     * Get Favorite by id
     *
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        return $this->showWithRelationTrait($this->model, $id, ['city', 'users']);
    }
    /**
     * Get Favorite by id
     *
     * @param $id
     * @return mixed
     */
    public function showUserWithRelation($id, $relation)
    {
        return $this->showWithRelationTrait($this->model, $id, $relation);
    }
    /**
     *  Validate User And Provider data.
     * Favorite to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */

    public function store($request)
    {
        return Auth::user()->favorites()->create($request->all());
    }

    /**
     *  Validate User And Provider data.
     * Favorite to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */

    public function addProduct($request)
    {
        return Auth::user()->favorites()->where('id', $request->list_id)->first()->items()->create(['product_id' => $request->product_id]);
    }
    public function deleteProduct($request)
    {        
     return  Auth::user()->favorites()
        ->where('id', $request->list_id)
        ->first()
        ->items()->where('product_id', $request->product_id)
        ->first()->delete();
    }
    
    /**
     * favorite products list.
     */
    public function fsavoriteProducts()
    {
        $favorite = Product::wherehas('favorites', function ($q) {
            $q->wherehas('favorite',  function ($q) {

                $q->where('user_id', Auth::id());
            });
        })
            ->get(['id', 'title', 'sku',]);


        return $favorite;
    }
    /**
     * Update
     * Favorite to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */

    public function update($request, $favorite)
    {
        return Auth::user()->favorites()->where('id', $favorite)->update(['name' => $request->name]);
    }
    /**
     * destroy User
     *
     * @param $data
     * @return User
     */
    public function destroy($id)
    {
        return Auth::user()->favorites()->where('id', $id)->delete();
    }

    /**
     * restore User
     *
     * @param $data
     * @return User
     */
    public function restore($id)
    {
        return $this->restoreTrait($this->model, $id);
    }

    /**
     * Get All Stores with the store of the week.
     */
    public function StoreOfTheWeek($id)
    {
        $favorite_of_the_week = $this->model
            ->where('is_store_of_the_week', 1)
            ->where('is_active', 1)
            ->orderBy('updated_at', 'desc')
            ->get(['id', 'name', 'logo'])->first();


        $favorites = QueryBuilder::for($this->model)
            ->defaultSort('-id')
            ->where('city_id', $id)
            ->where('is_active', 1)
            ->allowedFilters(['name'])
            ->allowedSorts(['name', 'is_active'])
            ->get(['id', 'name', 'logo', 'has_sales']);

        return ['store_of_the_week' => $favorite_of_the_week, 'store' => $favorites];
    }

    /**
     *7 New stores
     *7 Featured products
     *Store of the week with 7 of its products
     */
    public function newOfTheWeek($id)
    {
        $favorite_of_the_week = $this->model
            ->where('is_store_of_the_week', 1)
            ->where('is_active', 1)
            ->orderBy('updated_at', 'desc')
            ->with(['products:id,store_id,sku,title,price,offer_id', 'products.firstMedia'])
            ->latest()->take(7)
            ->get(['id', 'name', 'logo'])->first();


        $favorites = QueryBuilder::for($this->model)
            ->defaultSort('-id')
            ->where('city_id', $id)
            ->where('is_active', 1)
            ->latest()->take(7)
            ->get(['id', 'name', 'logo', 'has_sales']);

        $featured_products  = QueryBuilder::for(Product::class)
            ->defaultSort('-id')
            ->where('is_featured', 1)
            ->with(['firstMedia'])
            ->latest()->take(7)
            ->get(['id', 'store_id', 'sku', 'title', 'price','offer_id']);


        return ['store_of_the_week' => $favorite_of_the_week, 'featured_products' => $featured_products, 'store' => $favorites];
    }
    /**
     *Get the selected store with thier available genders
     */
    public function withGenders($favorite_id)
    {
        return $this->model
            ->where('id', $favorite_id)
            ->with('genders')
            ->first();
    }

    /**
     *    Follow a store and add it to the users followed stores
     */
    public function follow($favorite_id)
    {
        $favorite = $this->model
            ->where('id', $favorite_id)
            ->first();
        $following = $favorite->followrs()->syncWithoutDetaching([Auth::id()]);
        return  $following['attached'];
    }

    /**
     *Get stores categroies API
     */
    public function categroies($favorite_id, $gender_id)
    {
        return  $this->model->find($favorite_id)->categories()->wherehas('products')->where('gender_id', $gender_id)->get();
    }
    /**
     *Get category's sub categories of a store API
     */
    public function subcategories($favorite_id, $gender_id, $category_id)
    {
        $categroies = $this->model->find($favorite_id)->categories()->wherehas('products')->where('gender_id', $gender_id)->get();
        return $categroies->find($category_id)->subCategories()->get(['id', 'name']);
    }
    /**
     *Get category's products of a store API
     */
    public function products($favorite_id, $gender_id, $category_id)
    {
        return $this->model->find($favorite_id)
            ->products()
            ->where('gender_id', $gender_id)
            ->where('category_id', $category_id)->get();
    }


    /**
     *Get all products available colors to filter with
     */
    public function categoryColor($category_id)
    {
        $colors = [];
        $categroies = Category::find($category_id)
            ->products()->with('quantities.color')
            ->get()->toArray();
        // to preserve keys
        foreach ($categroies as  $value) {
            foreach ($value['quantities'] as $q) {
                $colors[] = $q['color'];
            }
        }
        return $colors;
    }
    /**
    //  *Get all products available sizes to filter with
    //  */
    public function categorySizes($category_id)
    {
        $sizes = [];
        $categroies = Category::find($category_id)
            ->products()->with('quantities')
            ->get()->toArray();
        // return  $categroies;
        // to preserve keys
        foreach ($categroies as  $value) {
            foreach ($value['quantities'] as $q) {
                $sizes[] = $q['size'];
            }
        }
        return $sizes;
    }
    /**
    //  *Get all system categories of a specific gender API
    //  */
    public function GenderCategories($city_id, $gender_id)
    {
        return Category::where('gender_id', $gender_id)->whereHas('stores', function ($query) use ($city_id) {
            $query->where('city_id', $city_id);
        })->get();
    }

    /**
    //  *get all main categories for all stores by city id
    //  */
    public function cityStoresCtegories($city_id)
    {
        return Category::whereHas('stores', function ($query) use ($city_id) {
            $query->where('city_id', $city_id);
        })->get();
    }


    /**
    //  *get categories for all stores by city id and gender id
    //  */
    public function genderCategoryProducts($gender_id, $category_id)
    {

        return  Product::where('category_id', $category_id)
            ->where('gender_id', $gender_id)
            ->get();
    }


    /**
    //  *Get category's subcategories of specific gender API
    //  */
    public function genderCategoriesSubcategories($gender_id, $category_id)
    {
        return SubCategory::whereHas('Category', function ($query) use ($gender_id, $category_id) {
            $query->where('id', $category_id)->where('gender_id', $gender_id);
        })->get();
    }
}
