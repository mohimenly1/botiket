<?php

namespace App\Repositories;

use App\Helpers\FileHelper;
use App\Http\Traits\CrudTrait;
use App\Http\Traits\MainTrait;
use App\Http\Traits\ResponseTraits;
use App\Models\BeamsNotification;
use App\Models\Brand;
use App\Models\BrandVisitors;
use App\Models\Category;
use App\Models\Gender;
use App\Models\Order;
use App\Models\Product;
use App\Models\Quantity;
use App\Models\Store;
use App\Models\SubCategory;
use Auth;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Handler\Proxy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\QueryBuilder;
use Tymon\JWTAuth\Facades\JWTAuth;

class StoreRepository
{
    use CrudTrait, ResponseTraits, MainTrait;
    /**
     * @var Store
     */
    protected $store;

    /**
     * UserRepository constructor.
     *
     * @param Store $store
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
    /**
     * Get all stores with Role.
     *
     * @return Store $store
     */
    public function index()
    {
        return QueryBuilder::for($this->model)
            ->defaultSort('-id')
            ->with('city')
            ->allowedFilters(['name', 'phone'])
            ->allowedSorts(['name', 'phone', 'city_id', 'is_active'])
            ->get(['id', 'name', 'logo', 'phone', 'city_id', 'is_active']);
    }
    /**
     * Get all stores with Role.
     *
     * @return Store $store
     */
    public function indexDeleted()
    {
        return QueryBuilder::for($this->model)
            ->defaultSort('-id')
            ->with('city')
            ->allowedFilters(['name', 'phone'])
            ->allowedSorts(['name', 'phone', 'city_id', 'is_active'])
            ->onlyTrashed()
            ->get(['id', 'name', 'logo', 'phone', 'city_id', 'is_active']);
        return $this->indexDeletedWithRelationGetTrait($this->model, ['city'], ['id', 'name', 'logo', 'phone', 'city_id', 'is_active']);
    }
    /**
     * Get store by id
     *
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        return $this->showWithRelationTrait($this->model, $id, ['city', 'users']);
    }
    /**
     * Get store by id
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
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */

    public function store($request)
    {
        $this->model->fill($request->all());

        $this->model->save();


        if ($request->hasFile('logo')) {
            $image_path = $request->file('logo')->store('/stores/' . $this->model->id , 's3');
            Storage::disk('s3')->setVisibility($image_path, 'public');
            $this->model->logo =Storage::disk('s3')->url($image_path);
            $this->model->save();
        }
        if ($request->has('admins_ids')) {
            //  dd($request->admins_ids);
            foreach ($request->admins_ids as $admin) {
                $this->model->users()->attach(['store_id' => $admin]);
            }
        };


        return $this->model->load('users');
    }
    /**
     * Update
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */

    public function update($request, $store)
    {
        $store = Store::findOrFail($store);
        $store->fill($request->all());

        if ($request->hasFile('logo')) {
            $delete_old_image = Storage::disk('s3')->delete($store->logo);
            if ($delete_old_image) {
                $image_path = $request->file('logo')->store('/stores/' . $this->model->id , 's3');
                Storage::disk('s3')->setVisibility($image_path, 'public');
                $store->logo =Storage::disk('s3')->url($image_path);
            }
        }
        if ($request->has('admins_ids')) {
            $store->users()->sync($request->admins_ids);
        };

        $store->save();

        return $store->load('users');
    }
    /**
     * destroy User
     *
     * @param $data
     * @return User
     */
    public function destroy($id)
    {
        $store = $this->model->find($id);
        $opend_orders = Order::where('id', $id)->whereIn('order_status_id', [1, 2, 3, 4])->get()->toArray();

        if (count($opend_orders) < 1) {
            if ($store) {
                // maybe it will cause an issue
                $store->products()->chunkById(200, function($result){
                    foreach ($result as $product) {
                        $product->delete();
                    }
                });
            }
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $this->destroyTrait($this->model, $id), 200);
        } else {
            $orders_array = [];
            foreach ($opend_orders as $item) {
                //  return $item;
                $orders_array[] = $item['id'];
            };
            return $this->prepare_response(__('auth.Something went wrong'), ('تأكد من اتمام او الغاء هذه الطلبات اولا ' . implode(", ",  array_values(array_unique($orders_array)))), null, 400);
        }
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
        $store_of_the_week = $this->model
            ->where('is_store_of_the_week', 1)
            ->where('is_active', 1)
            ->orderBy('updated_at', 'desc')
            ->get(['id', 'name', 'logo', 'created_at'])->first();


        $stores = QueryBuilder::for($this->model)
            ->defaultSort('-id')
            ->where('city_id', $id)
            ->where('is_active', 1)
            ->allowedFilters(['name'])
            ->allowedSorts(['name', 'is_active'])
            ->get(['id', 'name', 'logo', 'has_sales', 'created_at']);

        return ['store_of_the_week' => $store_of_the_week, 'store' => $stores];
    }
    /**
     *The search will be at the level of the products and stores table
     */
    public function mainSearch($search)
    {
        $searchData = [];
        $i = 0;
        $stors = Store::where(function ($query) use ($search) {
            return $query->where('name', 'LIKE', '%' . $search . '%')
                ->orWhere('description', 'LIKE', '%' . $search . '%');
        })
            ->latest()->take(5)->get(['id', 'name', 'logo'])->toArray();

        foreach ($stors as $store) {
            $searchData[$i]['id'] = $store['id'];
            $searchData[$i]['name'] = $store['name'];
            $searchData[$i]['image'] = $store['logo'];
            $searchData[$i]['type'] = 'store';
            $i++;
        }
        $products = Product::where(function ($query) use ($search) {
            return $query->where('sku', 'LIKE', '%' . $search . '%')
                ->orWhere('title', 'LIKE', '%' . $search . '%')
                ->orWhere('price', 'LIKE', '%' . $search . '%');
        })->with('firstMedia')
            ->latest()->take(40)->get(['id', 'title'])->toArray();
        foreach ($products as $product) {
            $searchData[$i]['id'] = $product['id'];
            $searchData[$i]['name'] = $product['title'];
            $searchData[$i]['image'] = $product['first_media'] != null ? $product['first_media']['path'] : null;
            $searchData[$i]['type'] = 'product';
            $i++;
        }

        return $searchData;
    }
    /**
     *7 New stores
     *7 Featured products
     *Store of the week with 7 of its products
     */
    public function newOfTheWeek()
    {
        $store_of_the_week = $this->model
            ->where('is_store_of_the_week', 1)
            ->where('is_active', 1)
            ->orderBy('updated_at', 'desc')
            ->with(['products:id,store_id,sku,title,price,offer_id,created_at', 'products.firstMedia'])
            ->latest()->take(7)
            ->get(['id', 'name', 'logo', 'background_image'])->first();


        $stores = QueryBuilder::for($this->model)
            ->defaultSort('-id')
            ->where('is_active', 1)
            ->latest()->take(7)
            ->with(['categories' => function ($query) {
                $query->latest()->limit(3);
            }])
            ->get(['id', 'name', 'logo', 'has_sales', 'background_image']);

        $featured_products  = QueryBuilder::for(Product::class)
            ->defaultSort('-id')
            ->where('is_featured', 1)
            ->with(['firstMedia', 'store:id,logo,background_image', 'brand:id,logo'])
            ->latest()->take(7)
            ->get(['id', 'store_id', 'sku', 'title', 'price', 'offer_id', 'brand_id']);
        foreach ($featured_products as $product) {
            if ($product->store_id != null) {
                // dd($product->store->logo);
                $product['logo'] = $product->store->logo;
            } elseif ($product->brand_id != null) {
                $product['logo'] = $product->brand->logo;
            } else {
                $product['logo'] = null;
            }
            unset($product['store']);
            unset($product['brand']);
        }

        return [
            'store_of_the_week' => $store_of_the_week,
            'featured_products' => $featured_products,
            'store' => $stores
        ];
    }
    /**
     *Get the selected store with thier available genders
     */
    public function withGenders($store_id)
    {
        $store = $this->model
            ->where('id', $store_id)
            ->first();


        $response = $this->model
            ->where('id', $store_id)
            ->with('city')
            ->with('genders', function ($query) use ($store_id) {
                $query->with('categories', function ($query) use ($store_id) {
                    $query->whereHas('products', function($q){
                        $q->limit(2);
                    })->whereHas('stores', function ($query) use ($store_id) {
                        $query->where('store_id', $store_id);
                    });
                });
            })
            ->first();
        $follow = 0;

        try {
            if ($user = JWTAuth::parseToken()->authenticate()) {
                $follow = $store->followrs()->where('user_id', $user->id)->count();
                $follow > 0 ? $follow = 1 : $follow = 0;
            }
        } catch (Exception $e) {
        }
        if (request()->has('token') && request()->token != null) {
            if ($user = JWTAuth::parseToken()->authenticate()) {
                $follow = $store->followrs()->where('user_id', $user->id)->count();
                $follow > 0 ? $follow = 1 : $follow = 0;
            }
        }

        $response['following'] = $follow;
        return $response;
    }

    /**
     *    Follow a store and add it to the users followed stores
     */
    public function follow($store_id)
    {
        $store = $this->model
            ->where('id', $store_id)
            ->first();
        $following = $store->followrs()->syncWithoutDetaching([Auth::id()]);
        return  $following['attached'];
    }

    /**
     *Get stores categroies API
     */
    public function categroies($store_id, $gender_id)
    {
        return  $this->model->find($store_id)->categories()->wherehas('products', function ($query) {
            $query->limit(2);
        })->where('gender_id', $gender_id)->get();
    }
    /**
     *Get category's sub categories of a store API
     */
    public function subcategories($store_id, $gender_id, $category_id)
    {
        $categroies = $this->model->find($store_id)->categories()->where('gender_id', $gender_id)->get();
        return $categroies->find($category_id)->subCategories()->get(['id', 'name']);
    }
    /**
     *Get category's products of a store API
     */
    public function products($store_id, $gender_id, $category_id)
    {
        return $this->model->find($store_id)
            ->products()
            ->where('gender_id', $gender_id)
            ->where('category_id', $category_id)->with('firstMedia')->paginate(50);
    }


    /**
     *Get all products available colors to filter with
     */
    public function categoryColor($category_id)
    {
        $colors = [];
        $products = Brand::find($category_id)
            ->products()
            ->with('quantities.color');

        $products->chunk(200, function ($result) use (&$colors) {
            $categroies = $result;
            // to preserve keys
            foreach ($categroies as  $value) {
                foreach ($value['quantities'] as $q) {
                    $colors[] = $q['color'];
                }
            }
        });
        return $this->unique_multidim_array($colors, 'id');
    }
    /**
     *Get all products available sizes to filter with
     */
    public function categorySizes($category_id)
    {

        $sizes = [];
        $products = Brand::find($category_id)
            ->products()
            ->with('quantities');
            
        $products->chunk(200, function ($result) use (&$sizes) {
            $categroies = $result;
            // to preserve keys
            foreach ($categroies as  $value) {
                foreach ($value['quantities'] as $q) {
                    $sizes[] = $q['size'];
                }
            }
        });
        return array_values(array_unique($sizes));

    }


    public function unique_multidim_array($array, $key)
    {
        $temp_array = array();
        $i = 0;
        $key_array = array();

        foreach ($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $temp_array[] = $val;
            }
            $i++;
        }
        return $temp_array;
    }
    /**
     *Get all products available colors to filter with
     */
    public function categoryStoreColors($category_id, $store_id)
    {
        $colors = [];
        $categroies = 
            Product::where( "category_id" ,$category_id)->where('store_id', $store_id);
        $categroies->chunk(200, function($result) use(&$colors){
        // to preserve keys
        foreach ($result as  $value) {
            foreach ($value['quantities'] as $q) {
                $colors[] = $q['color'];
            }
        }
        });


        return $this->unique_multidim_array($colors, 'id');
    }
    /**
    //  *Get all products available sizes to filter with
    //  */
    public function categoryStoreSizes($category_id, $store_id = null)
    {
        $sizes = [];
        $categroies = Category::find($category_id)
            ->products()->where('store_id', $store_id)
            ->with('quantities')
            ->distinct();
        //  return  $categroies;

        $categroies->chunk( 200, function($result) use(&$sizes){
        // to preserve keys
        foreach ($result as  $value) {
            foreach ($value['quantities'] as $q) {
                $sizes[] = $q['size'];
            }
        }
        });

        return array_values(array_unique($sizes));
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
    //  *Get all system categories of a specific gender API
    //  */
    public function  brandGendersCategories($brand_id)
    {
        $start_of_current_month = Carbon::now()->startOfMonth();
        $end_of_current_month = Carbon::now()->endOfMonth();


        
        $brand_visitor = BrandVisitors::where('brand_id', $brand_id)
            ->whereBetween(
                'created_at',
                [$start_of_current_month, $end_of_current_month]
            )
            ->increment('counter', 1);
        $brand = Brand::findOrFail($brand_id);


        // TO BE CHUNKED ----
        /*
        $products = Product::where('brand_id', $brand_id);
        $tempArray = [];

        $products->chunk(200, function($res) use(&$tempArray) {
            foreach ($res as $product) {
              $tempArray[] = $product->gender_id;
            }
             $tempArray = array_unique($tempArray);
        });
        */

       
       
       // $genders = $products->pluck('gender')->toArray();

        //$tempArray = array_unique(array_column($genders, 'id'));
        // return $tempArray;
        $moreUniqueArray = Gender::select()->
        //whereIn('id', array_values($tempArray))
            with('categories', function ($query) use ($brand_id) {

                $query->whereHas('products', function ($query) use ($brand_id) {
                    $query->limit(2)->where('brand_id', $brand_id);
                });
            })->get();

        $brand['genders'] = $moreUniqueArray;

        return ($brand);
    }

    /**
    //  *Get ALL system categories for a GENDER
    //  */
    public function  AllGenderCategories($gender_id)
    {
        // will get all the categories for a gender without taking into consideration whether or not products exist in that category
        $categories = Category::where('gender_id', $gender_id)->get();
        return ($categories);
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
    public function genderCategoryProducts($city_id, $gender_id, $category_id)
    {
        return  Product::where('category_id', $category_id)
            ->where('gender_id', $gender_id)
            ->where('brand_id', null)
            ->whereHas('store', function ($q) use ($city_id) {
                $q->where('city_id', $city_id);
            })->with('firstMedia')->paginate(50);
    }


    /**
     *Get category's subcategories of specific gender API
     */
    public function genderCategoriesSubcategories($city_id, $gender_id, $category_id)
    {
        return SubCategory::whereHas('Category', function ($query) use ($gender_id, $category_id, $city_id) {
            $query->whereHas('stores', function ($q) use ($city_id) {
                $q->where('city_id', $city_id);
            })->where('id', $category_id)
                ->where('gender_id', $gender_id);
        })->get();
    }
    /**
     *Get category's subcategories of specific gender API
     */
    public function brandCategoryProducts($brand_id, $category_id)
    {
        return  Product::where('category_id', $category_id)
            ->where('brand_id', $brand_id)->with('firstMedia')->orderBy('created_at', 'desc')->paginate(10);
    }



    /**
     *Get all system products of a specific gender API
        === create date: Jan-10th-2022
     */
    public function brandGenderProducts($brand_id, $gender_id)
    {
        return  Product::where('gender_id', $gender_id)
            ->where('brand_id', $brand_id)->with('firstMedia')->orderBy('created_at', 'desc')->paginate(24);
    }

    /**
     *Get all products from all brands based on category and gender
        === create date: Feb-5th-2022
     */
    public function brandProductsGenderCategories($category_id)
    {
        $output = $this->getProductsAndQuantities($category_id);
        $data = $output['products'];
        return  $data;
    }

    /**
     *Get all products from all brands based on category and gender
        === create date: Feb-9th-2022
     */
    public function colorsSizesForCategory($category_id)
    {
        $output = $this->getProductsAndQuantities($category_id);
        $data['colors']  = array_values(array_unique($output['colors']));
        $data['sizes'] = array_values(array_unique($output['sizes']));
        return  $data;
    }
    /**
    ===custom function
     **this functions serves the purpose of ANTI-Repetition
     */
    public function getProductsAndQuantities($category_id)
    {
        $output = [];
        $colors = [];
        $sizes = [];
        // dd(Product::where('category_id', $category_id)->where('brand_id', '!=', null)->get());
        $products = Product::where('category_id', $category_id)->with("firstMedia")
            ->where('brand_id', '!=', null)->paginate(50);

            foreach ($products->items() as $product) {
                $quantities[] = $product->quantities;
            }


            for ($i = 0; $i < count($products->items()); $i++) {
                $j = 0;
                for ($j = 0; $j < count($quantities[$i]); $j++) {
                    $sizes[] = $quantities[$i][$j]['size'];
                    $colors[] = $quantities[$i][$j]->color;
                }
            }



                $output['products'] = $products;
                $output['colors'] = $colors;
                $output['sizes'] = $sizes;
            
        return $output;
    }


    /**
    //  *Filter all products from all brands based on category, color and size
    === create date: Feb-10th-2022
    //  */
    public function filterColorsSizesForCategory($request)
    {

        $products = Product::when(
            $request->has('color_id'),
            function ($query) use ($request) {
                return $query->WhereHas('quantities', function ($query) use ($request) {
                    return $query->whereIn('color_id', $request->color_id);
                });
            }
        )->when(
            $request->has('sizes'),
            function ($query) use ($request) {
                return $query->WhereHas('quantities', function ($query) use ($request) {
                    return $query->whereIn('size', $request->sizes);
                });
            }
        )->when(
            $request->has('category_id'),
            function ($query) use ($request) {
                return $query->Where('category_id', $request->category_id);
            }
        )->where('brand_id', '!=', null)
            ->with('firstMedia', 'quantities')
            ->paginate(50);

        return $products;
    }

    /**
     *Get all products available colors to filter with
     */
    public function brandColors($brand_id, $category_id)
    {

        $colors = [];
        $products = Brand::find($brand_id)
            ->products()->where('category_id', $category_id);
           // ->with('quantities.color');

        $products->chunk(200, function ($result) use (&$colors) {
            $categroies = $result;
            // to preserve keys
            foreach ($categroies as  $value) {
                foreach ($value['quantities'] as $q) {
                    $colors[] = $q['color'];
                }
            }
        });
        return $this->unique_multidim_array($colors, 'id');
    }
    /**
     *Get all products available sizes to filter with
     */
    public function brandSizes($brand_id, $category_id)
    {
        $sizes = [];
        $products = Brand::find($brand_id)
            ->products()->where('category_id', $category_id);
          //  ->with('quantities.color');
        $products->chunk(200, function ($result) use (&$sizes) {
            $categroies = $result;
            // to preserve keys
            foreach ($categroies as  $value) {
                foreach ($value['quantities'] as $q) {
                    $sizes[] = $q['size'];
                }
            }
        });
        return array_values(array_unique($sizes));
    }
}
