<?php

namespace App\Repositories;

use App\Helpers\FileHelper;
use App\Http\Traits\CrudTrait;
use App\Http\Traits\MainTrait;
use App\Http\Traits\ResponseTraits;
use App\Models\BeamsNotification;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Gender;
use App\Models\Offer;
use App\Models\Category;
use App\Models\Color;
use App\Models\Currency;
use App\Models\OrderItem;
use App\Models\Quantity;
use App\Models\SubCategory;
use Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;

class ProductRepository
{
    use CrudTrait, ResponseTraits, MainTrait;
    /**
     * @var Delivery
     */
    protected $brand;


    /**
     * UserRepository constructor.
     *
     * @param Delivery $brand
     */
    public function __construct(Model $model)
    {

        $this->model = $model;
    }
    /**
     * Get all brands with Role.
     *
     * @return Delivery $brand
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->role == 'super-admin') {
            $model =  Product::with(['gender:id,name', 'category:id,name', 'subCategory:id,name', 'firstMedia'])
                ->whereNotNull('brand_id')
                ->whereNull('store_id')
                ->orWhereHas('store', function ($query) {
                    $query->where('class_a_access', '=', 1);
                })
                ->filter(Request()->all())
                ->paginate(10, ['id', 'highlight', 'title', 'sku', 'price', 'offer_id', 'gender_id', 'category_id', 'sub_category_id']);
            return $model;
        } elseif ($user->role == 'store-admin') {
            $model =  Product::with(['gender:id,name', 'category:id,name', 'subCategory:id,name', 'firstMedia'])
                ->filter(Request()->only("search"))
                ->where('store_id', $user->store()->first()->id)
                ->with(['gender:id,name', 'category:id,name', 'subCategory:id,name', 'firstMedia'])
                ->paginate(10, ['id', 'highlight', 'title', 'sku', 'price', 'offer_id', 'gender_id', 'category_id', 'sub_category_id']);
            return $model;
        }
    }


    public function update_price($rate1, $rate2, $fee, Product $product)
    {


        $discount_rate = $this->get_discount_rate($product);
        $new_price = (($product->original_price * $rate1) * $rate2) * $fee;
        $new_discounted_price = $new_price * $discount_rate;


        $product->price = $new_price;
        $product->discount_price = $new_discounted_price;

        return $product;
    }

    public function get_discount_rate(Product $product)
    {

        return ($product->discount_price) / ($product->price);
    }




    public function filterProducts($request)
    {

        $products = $this->model->when(
            $request->query('color_id'),
            function ($query) use ($request) {
                return $query->WhereHas('quantities', function ($query) use ($request) {
                    return $query->whereIn('color_id', $request->query('color_id'));
                });
            }
        )->when(
            $request->query('sizes'),
            function ($query) use ($request) {
                return $query->WhereHas('quantities', function ($query) use ($request) {
                    return $query->whereIn('size', $request->query('sizes'));
                });
            }
        )->when(
            $request->query('store_id'),
            function ($query) use ($request) {
                return $query->Where('store_id', $request->query('store_id'))->where('brand_id', null);
            }
        )->when(
            $request->query('city_id'),
            function ($query) use ($request) {
                return $query->WhereHas('store', function ($query) use ($request) {
                    return $query->where('city_id', $request->query('city_id'));
                });
            }
        )->when(
            $request->query('category_id'),
            function ($query) use ($request) {
                return $query->Where('category_id', $request->query('category_id'));
            }
        )->when(
            $request->query('brand_id'),
            function ($query) use ($request) {
                return $query->Where('brand_id', $request->query('brand_id'));
            }
        )->when(
            $request->query('gender_id'),
            function ($query) use ($request) {
                return $query->Where('gender_id', $request->query('gender_id'));
            }
        )->when(
            $request->query('title'),
            function ($query) use ($request) {
                return $query->Where('title', 'like', '%' . $request->query('title') . '%');
            }
        )->when(
            $request->query('is_shipped'),
            function ($query) use ($request) {
                return $query->Where('is_shipped', true);
            }
        )
            ->with('category', 'subCategory', 'quantities', 'store', 'firstMedia', 'brand')
            ->paginate(10, ['id', 'title', 'sku', 'price', 'offer_id', 'gender_id', 'category_id', 'sub_category_id', 'store_id', 'brand_id', 'created_at']);

        return $products;
    }

    /**
     * Get all brands with Role.
     *
     * @return Delivery $brand
     */
    public function skus($search)
    {
        $user = Auth::user();

        if ($user->role == 'super-admin') {
            return QueryBuilder::for($this->model)
                ->defaultSort('-id')
                ->where('sku', 'LIKE', "%{$search}%")
                ->whereNull('store_id')
                ->whereNotNull('brand_id')
                ->allowedSorts(['id', 'sku', 'title'])
                ->limit(1000)
                ->get(['id', 'sku', 'title', 'discount_price', 'offer_id', 'price']);
        } elseif ($user->role == 'store-admin') {
            return $this->model->orderBy('id', 'desc')
                ->where('store_id', $user->store()->first()->id)
                ->where('sku', 'LIKE', "%{$search}%")
                ->whereNull('brand_id')
                ->limit(1000)
                ->get(['id', 'sku', 'title']);
        }
    }


    public function search($request)
    {
        $search = $request->value;
        $products = Product::whereNull('store_id')->where(function ($query) use ($search) {
            return $query->where('sku', $search);
        })->with(['gender:id,name', 'category:id,name', 'subCategory:id,name', 'firstMedia'])->paginate(10);
        return $products;
    }

    /**
     * Get all brands with Role.
     *
     * @return Delivery $brand
     */
    public function indexDeleted()
    {
        $user = Auth::user();
        if ($user->role == 'super-admin') {
            return  QueryBuilder::for($this->model)
                ->defaultSort('-id')
                ->whereNull('store_id')
                ->whereNotNull('brand_id')
                ->with(['category:id,name', 'subCategory:id,name', 'firstMedia'])
                ->onlyTrashed()
                ->allowedFilters(['title', 'sku'])
                ->allowedSorts(['title', 'sku', 'price', 'offer_id', 'gender_id', 'category_id', 'sub_category_id'])
                ->paginate(10, ['id', 'title', 'sku', 'price', 'offer_id', 'category_id', 'sub_category_id']);
        } elseif ($user->role == 'store-admin') {
            return QueryBuilder::for($this->model)
                ->defaultSort('-id')
                ->where('store_id', $user
                    ->store()->first()->id)
                ->whereNull('brand_id')
                ->with(['category:id,name', 'subCategory:id,name', 'firstMedia'])
                ->onlyTrashed()
                ->allowedFilters(['title', 'sku'])
                ->allowedSorts(['title', 'sku', 'price', 'offer_id', 'gender_id', 'category_id', 'sub_category_id'])
                ->paginate(10, ['id', 'title', 'sku', 'price', 'offer_id', 'category_id', 'sub_category_id']);
        }
    }

    public function show($id)
    {

        return $this->showTrait($this->model, $id);
    }
    /**
     * Get brand by id
     *
     * @param $id
     * @return mixed
     */
    public function showWithRelation($id, $relation)
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

    public function generateSKU($gender, $category, $sub_category, $brand)
    {
        //generating a timestamp
        $timestamp = substr(date_timestamp_get(date_create()), 6);
        // changing gender from numbers to letters
        switch ($gender) {
            case 1:
                $gender = 'M';
                break;
            case 2:
                $gender = 'F';
                break;
            case 3:
                $gender = 'K';
                break;
        }
        // generating a unnique SKU for the product based on a blend of keys and a timestamp
        $SKU = $gender . $category . $sub_category . $brand . '-' . $timestamp;
        return $SKU;
    }

    public function store($request)
    {
        DB::beginTransaction();
        //SKU Generator
        $this->model->sku = $this->generateSKU($request->gender_id, $request->category_id, $request->sub_category_id, $request->brand_id);
        $this->model->OEM = $request->OEM;
        $this->model->url = $request->url;
        $this->model->title = $request->title;
        $this->model->description = $request->description;
        $this->model->price =  $request->price != null ? ceil($request->price) : $request->price;
        $this->model->original_price =  $request->original_price != null ? ceil($request->original_price) : $request->original_price;
        $this->model->discount_price =  $request->discount_price != null ? ceil($request->discount_price) : $request->discount_price;
        $this->model->is_shipped = $request->is_shipped;
        $this->model->is_featured = $request->is_featured;
        $this->model->brand_id = $request->brand_id;
        $this->model->sub_category_id = $request->sub_category_id;
        $this->model->category_id = $request->category_id;
        $this->model->gender_id = $request->gender_id;
        $this->model->offer_id = $request->offer_id;
        $this->model->highlight = $request->highlight;
        $this->model->season = $request->season;

        if ($this->model->discount_price > $this->model->price || $this->model->discount_price < ($this->model->price * 0.05))
            throw new \ErrorException('discount_price not valid: must be >=price || <= price*0.25 ');


        $user = Auth::user();
        if ($user->role == 'super-admin') {
            $this->model->store_id = null;
        } elseif ($user->role == 'store-admin') {
            $this->model->store_id = $user->store()->first()->id;
            $user->store()->first()->categories()->syncWithoutDetaching($request->category_id);
            $user->store()->first()->genders()->syncWithoutDetaching($request->gender_id);
        }
        $this->model->save();
        if ($request->has('medias')) {
            foreach ($request->medias as $key => $media) {
                $color_id = Color::where('color_value', $media['color'])->first();

                $image_path = $media['file']->store('/products/' . $this->model->id  . '/' . $this->model->sku . '-' . $key, 'public');

                Storage::disk('public')->setVisibility($image_path, 'public');

                $this->model->medias()->create(
                    [
                        'path' => Storage::disk('public')->url($image_path),
                        'color_id' => $color_id->id
                    ]
                );
            }
        }

        if ($request->has('quantities')) {
            foreach ($request->quantities as $quantity) {
                $color_id = Color::where('color_value', $quantity['color'])->first();
                $this->model->quantities()->create([
                    'size' => $quantity['size'],
                    'color_id' => $color_id->id,
                    'quantity' => $quantity['quantity'],
                ]);
            }
        }
        $this->model->save();
        DB::commit();

        return $this->model;
    }
    /**
     * Update Profile
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */

    public function update($request, $product)
    {
        $product = Product::findOrFail($product);
        $product->fill($request->all());

        if ($product->discount_price > $product->price || $product->discount_price < ($product->price * 0.25))
            throw new \ErrorException('discount_price not valid: must be >=price || <= price*0.25 ');


        //just in case rounding up
        $product->price != null && $product->price = ceil($product->price);
        $product->original_price != null && $product->original_price = ceil($product->original_price);
        $product->discount_price != null && $product->discount_price = ceil($product->discount_price);

        $product->save();

        //this got me an error on my personal laptop
        if ($request->has('medias')) {
            foreach ($request->medias as  $key => $media) {

                $old_media = $product->medias()->where('id', $key)->first();

                if ($media["file"] == 0 && $old_media) {
                    $delete_old_image = Storage::disk('s3')->delete($old_media->path);
                    $old_media->delete();
                } elseif ($old_media) {
                    $color_id = Color::where('color_value', $media['color'])->first();
                    $delete_old_image = Storage::disk('s3')->delete($old_media->path);


                    $image_path = $media['file']->store('/products/' . $product->id, 's3');
                    Storage::disk('s3')->setVisibility($image_path, 'public');

                    $old_media->update(['path' => Storage::disk('s3')->url($image_path), "color_id" => $color_id->id]);
                } elseif ($media["file"] != 0 && !$old_media) {
                    $color_id = Color::where('color_value', $media['color'])->first();
                    $image_path = $media['file']->store('/products/' . $product->id, 's3');
                    Storage::disk('s3')->setVisibility($image_path, 'public');
                    $product->medias()->create(['path' => Storage::disk('s3')->url($image_path), "color_id" => $color_id->id]);
                }
            }
        }
        if ($request->has('quantities')) {
            foreach ($request->quantities as $key => $quantity) {

                $old_quantity = $product->quantities()->where('id', $key)->first();

                if ($quantity == 0 && $old_quantity) {
                    $old_quantity->delete();
                } elseif ($old_quantity) {
                    $color_id = Color::where('color_value', $quantity['color_id'])->first();
                    $old_quantity->update(
                        [
                            'size' => $quantity['size'],
                            'color_id' => $color_id->id,
                            'quantity' => $quantity['quantity'],
                        ]
                    );
                } elseif ($quantity != 0 && !$old_quantity) {
                    $color_id = Color::where('color_value', $quantity['color_id'])->first();
                    $product->quantities()->create([
                        'size' => $quantity['size'],
                        'color_id' => $color_id->id,
                        'quantity' => $quantity['quantity'],
                    ]);
                }
            }
        }
        return $product;
    }
    /**
     * destroy User
     *
     * @param $data
     * @return User
     */
    public function destroy($id)
    {
        $opend_orders = Product::where('id', $id)->whereHas('orderItems', function ($q) {
            $q->whereHas('order', function ($q) {
                $q->whereIn('order_status_id', [1, 2, 3, 4]);
            });
        })->with('orderItems.order')->get();

        if (count($opend_orders->toArray()) < 1) {
            $product = Product::find($id);
            if ($product) {
                $product->medias()->delete();
                // $product->quantities()->delete();
                $product->cartusers()->delete();
                $product->favorites()->delete();
                $product->WishlistItems()->delete();
                $product->favorites()->delete();
            }
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $this->destroyTrait($this->model, $id), 200);
        } else {
            $orders_array = [];
            foreach ($opend_orders->pluck('orderItems') as $item) {
                foreach ($item as $order) {
                    $orders_array[] = ($order->order_id);
                }
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
     * Display a listing of the Product data.
     */
    public function quantities($id)
    {

        $price = $this->model->where('id', $id)->pluck('price');

        $local_currency = Currency::find(69);
        $currency = $this->model->find($id)->currency;

        $quantities = Quantity::where('product_id', $id)
            ->with('color')
            ->orderBy('id', 'desc')
            ->get()
            ->groupBy('color.id');

        $newquantities = [];
        $i = 0;
        foreach ($quantities as $key => $quantity) {
            $newquantities[$i]["id"] = $quantity[0]['color']['id'];
            $newquantities[$i]["name"] = $quantity[0]['color']['name'];
            $newquantities[$i]["color_value"] = $quantity[0]['color']['color_value'];
            foreach ($quantity as $item) {
                $newquantities[$i]['quantities'][] = $item->only(['id', 'size', 'quantity']);
            }
            $i++;
        }
        return ['price' => $price[0], 'quantities' => $newquantities, 'currency' => $currency, 'local_currency' => $local_currency];
    }
    /**
     * Display a listing of the Product data.
     */
    public function data()
    {
        return Gender::select('id', 'name')
            ->with(['categories' => function ($query) {
                $query->select('id', 'name', 'gender_id')->with('subCategories:id,name,category_id');
            }])
            ->orderBy('id', 'desc')->get();
    }
    /**
     * Display a listing of the brands.
     */
    public function brands()
    {
        return Brand::orderBy('id', 'desc')->with('originalCurrency', 'sellingCurrency')->get(['id', 'name', 'original_currency_id', 'selling_currency_id', 'increase_percentage']);
    }
    /**
     * Display a listing of the colors.
     */
    public function colors()
    {
        return Color::orderBy('id', 'desc')->get(['id', 'name', 'color_value']);
    }

    /**
     * Display a listing of the genders.
     */
    public function genders()
    {
        $user = Auth::user();
        if ($user->role == 'super-admin') {
            return Gender::orderBy('id', 'desc')->get(['id', 'name']);
        } elseif ($user->role == 'store-admin') {
            return $user->store()->first()->genders()->get(['genders.id', 'genders.name']);;
        }
    }
    /**
     * Display a listing of the offers.
     */
    public function offers()
    {
        $user = Auth::user();
        if ($user->role == 'super-admin') {
            return Offer::orderBy('id', 'desc')->get(['id', 'name']);
        } elseif ($user->role == 'store-admin') {
            return $user->store()->first()->offers()->orderBy('id', 'desc')->get(['id', 'name']);
        }
    }
    /**
     * Display a listing of the main Categories.
     */
    public function mainCategories($gender)
    {
        $user = Auth::user();
        if ($user->role == 'super-admin') {

            $gender = Gender::findOrFail($gender);
            return $gender->categories()->wherehas('products', function ($q) {
                return $q->take(2);
            })->orderBy('id', 'desc')->get(['id', 'name']);
        } elseif ($user->role == 'store-admin') {
            return $user->store()->first()->categories()->wherehas('products', function ($q) {
                return $q->take(2);
            })->get(['categories.id', 'categories.name']);
        }
    }
    /**
     * Display a listing of the sub Categories.
     */
    public function subCategories($category)
    {
        $Category = Category::findOrFail($category);
        return $Category->subCategories()->orderBy('id', 'desc')->get(['id', 'name']);
    }

    /**
     * Display a listing of the resource with Filtering.
     */
    public function filter($request)
    {
        $user = Auth::user();
        if ($user->role == 'super-admin') {
            return $this->model->orderBy('id', 'desc')
                ->select('id', 'title')
                ->whereNull('store_id')
                ->whereNotNull('brand_id')
                ->where('category_id', $request->category_id)
                ->where('gender_id', $request->gender_id)
                ->with('firstMedia')
                ->paginate(10);
        } elseif ($user->role == 'store-admin') {
            return $this->model->orderBy('id', 'desc')
                ->select('id', 'title')
                ->where('store_id', $user->store()->first()->id)
                ->whereNull('brand_id')
                ->where('category_id', $request->category_id)
                ->where('gender_id', $request->gender_id)
                ->with('firstMedia')
                ->paginate(10);
        }
    }

    public function report($request)
    {
        /*
        $products =  $this->model->select('id', 'title', 'price', 'offer_id')
            ->whereIn('id', $request->products)
            ->With('orderItems', function ($q) {
                $q->select('id', 'order_id', 'product_id', 'quantity')
                    ->WhereHas(
                        'order',
                        function ($q) {
                            $q->where('order_status_id', 5)
                                ->select('id', 'created_at', 'order_status_id');
                        }
                    );
            })->get();

        foreach ($products as $product) {
            $product['Sold quantity'] = array_sum(array_column($product->orderItems->all(), 'quantity'));
            unset($product['orderItems']);
            // return $product;
        }
        */

        $products =  $this->model->select('id', 'title', 'price', 'offer_id')
            ->whereIn('id', $request->products)->get();

        foreach ($products as $product) {
            $product['Sold quantity'] = 0;

            OrderItem::where("product_id", $product->id)

                ->chunkById(200, function ($items) use (&$product) {
                    foreach ($items as $item) {
                        if (isset($item->order) && $item->order->order_status_id == 5) {
                            $product['Sold quantity'] = $product['Sold quantity'] + $item->quantity;
                        }
                    }
                });
        }

        return $products;
    }
    /************************** Class B **********************************/

    /**
     * Display a listing of the resource.
     */
    public function classBIndex($request)
    {
        $index =  QueryBuilder::for($this->model)
            ->defaultSort('-id')
            ->where('store_id', $request->store_id);

        if ($request->gender_id != null) {
            $index = $index->where('gender_id', $request->gender_id);
        }

        if ($request->category_id != null) {
            $index = $index->where('category_id', $request->category_id);
        }
        if ($request->sub_category_id != null) {
            $index = $index->where('sub_category_id', $request->sub_category_id);
        }
        $index = $index->with(['category:id,name', 'subCategory:id,name', 'firstMedia'])
            ->allowedFilters(['title', 'sku'])
            ->allowedSorts(['title', 'sku', 'price', 'offer_id', 'gender_id', 'category_id', 'sub_category_id'])

            ->paginate(10, ['id', 'title', 'sku', 'price', 'offer_id', 'category_id', 'sub_category_id', 'store_id']);
        return $index;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function classBStore($request)
    {
        $this->model->sku = $request->sku;
        $this->model->OEM = $request->OEM;
        $this->model->title = $request->title;
        $this->model->description = $request->description;
        $this->model->price = $request->price;
        $this->model->discount_price = $request->discount_price;
        $this->model->original_price = $request->original_price;
        $this->model->url = $request->url;
        $this->model->is_shipped = $request->is_shipped;
        $this->model->is_featured = $request->is_featured;
        $this->model->brand_id = $request->brand_id;
        $this->model->sub_category_id = $request->sub_category_id;
        $this->model->category_id = $request->category_id;
        $this->model->gender_id = $request->gender_id;
        $this->model->offer_id = $request->offer_id;
        $this->model->store_id = $request->store_id;
        $this->model->save();
        if ($request->has('medias')) {
            foreach ($request->medias as $media) {
                $color_id = Color::where('color_value', $media['color'])->first();

                $image_path = $media['file']->store('/products/' . $this->model->id, 's3');
                Storage::disk('s3')->setVisibility($image_path, 'public');

                $this->model->medias()->create(
                    [
                        'path' => Storage::disk('s3')->url($image_path),
                        'color_id' => $color_id->id
                    ]
                );
            }
        }

        if ($request->has('quantities')) {
            foreach ($request->quantities as $quantity) {
                $color_id = Color::where('color_value', $quantity['color'])->first();
                $this->model->quantities()->create([
                    'size' => $quantity['size'],
                    'color_id' => $color_id->id,
                    'quantity' => $quantity['quantity'],
                ]);
            }
        }
        $this->model->save();

        return $this->model->id;
    }


    /**
     * Update the specified resource in storage.
     */
    public function classBUpdate($request, $id)
    {
        return $this->product->classBUpdate($request, $id);
    }

    /**
     * get product Details.
     */
    public function productDetails($id)
    {
        $product = $this->model
            ->where('id', $id)
            ->with([
                'medias',
                'collection.products.firstMedia',
                'collection.products:id,title,description,price',
                'subCategory'
                => function ($query) {
                    return $query->limit(5)->with(["products" => function ($q) {
                        return $q->limit(5)->with(["firstMedia"]);
                    }]);
                },
                'subCategory.products:id,sub_category_id,title,description,price',
                'brand',
                'store:id,name,logo,background_image'

            ])->first();



        if ($product) {
            //reshape product quantities

            $quantities = $product->quantities()
                ->with('color')
                ->orderBy('id', 'desc')
                ->get()
                ->groupBy('color.id');
            $quantitiesBySize = $product->quantities()
                ->with('color')
                ->orderBy('id', 'desc')
                ->get()
                ->groupBy('size');
            $newquantities = $size_quantities = [];
            $i = $j = 0;
            foreach ($quantities as $key => $quantity) {
                $newquantities[$i]["id"] = $quantity[0]['color']['id'];
                $newquantities[$i]["name"] = $quantity[0]['color']['name'];
                $newquantities[$i]["color_value"] = $quantity[0]['color']['color_value'];
                foreach ($quantity as $item) {
                    $newquantities[$i]['quantities'][] = $item->only(['id', 'size', 'quantity']);
                }
                $i++;
            }
            foreach ($quantitiesBySize as $key => $quantity) {
                $size_quantities[$j]["size"] = $quantity[0]['size'];
                foreach ($quantity as $item) {
                    $size_quantities[$j]['quantities'][] = array_merge($item->color->only(['id', 'name', 'color_value']), $item->only(['quantity']));
                }
                $j++;
            }
            $product['quantities'] = $newquantities;
            $product['size_quantities'] = $size_quantities;

            $collection = $product->collection->pluck('products')->toArray();
            //reshape product collection producta
            $collection = $product->collection->pluck('products')->where('id', '!=', $product->id)->take(7)->toArray();
            $newCollectionProducts = array_reduce($collection, 'array_merge', array());
            foreach ($newCollectionProducts as $key => $array_product) {

                if ($array_product['id'] == $product->id) {
                    unset($newCollectionProducts[$key]);
                }
            }
            $moreUniqueArray = $this->unique_multidim_array(array_values($newCollectionProducts), 'id');
            unset($product['collection']);
            $product['collection'] = $moreUniqueArray;


            $subCategory = $product['subCategory']['products']->take(7)->toArray();
            if (($key = array_search($product->id, array_column($subCategory, 'id'))) !== false) {
                unset($subCategory[$key]);
            }
            unset($product['subCategory']);
            $product['subCategories'] = $this->unique_multidim_array(array_values($subCategory), 'id');


            $favorite = $wishlist = 0;

            try {
                if ($user = JWTAuth::parseToken()->authenticate()) {
                    $favorite = Product::where('id', $product->id)->wherehas('favorites', function ($q) {
                        $q->wherehas('favorite', function ($q) {
                            $q->where('user_id', Auth::id());
                        });
                    })->count();
                    $favorite > 0 ? $favorite = 1 : $favorite = 0;
                }
            } catch (Exception $e) {
            };
            try {
                if ($user = JWTAuth::parseToken()->authenticate()) {
                    $wishlist = Product::where('id', $product->id)->wherehas('WishlistItems', function ($q) {
                        $q->where('user_id', Auth::id());
                    })->count();
                    $wishlist > 0 ? $wishlist = 1 : $wishlist = 0;
                }
            } catch (Exception $e) {
            };

            if (request()->has('token') && request()->token != null) {
                if ($user = JWTAuth::parseToken()->authenticate()) {
                    $favorite = Product::where('id', $product->id)->wherehas('favorites', function ($q) {
                        $q->wherehas('favorite', function ($q) {
                            $q->where('user_id', Auth::id());
                        });
                    })
                        ->count();
                    $favorite > 0 ? $favorite = 1 : $favorite = 0;

                    $wishlist = Product::where('id', $product->id)->wherehas('WishlistItems', function ($q) {
                        $q->where('user_id', Auth::id());
                    })->count();
                    $wishlist > 0 ? $wishlist = 1 : $wishlist = 0;
                }
            }
            if ($product->store_id != null) {
                $product['logo'] = $product->store->logo;
            } elseif ($product->brand_id != null && $product->brand) {
                $product['logo'] = $product->brand->logo;
            } else {
                $product['logo'] = null;
            }
            $product['favorite'] = $favorite;
            $product['wishlist'] = $wishlist;
        }

        return $product;
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
    public function skuProductDetails($sku)
    {
        $product = $this->model
            ->where('sku', $sku)
            ->with([
                'medias', 'collection.products.firstMedia', 'collection.products:id,title,description,price,offer_id',
                'subCategory' => function ($query) {
                    return $query->limit(5)->with(["products" => function ($q) {
                        return $q->limit(5)->with(["firstMedia"]);
                    }]);
                },
                'subCategory.products:id,sub_category_id,title,description,price,offer_id',
                'store:id,logo'
            ])->first();
        if ($product) {
            //reshape product quantities

            $quantities = $product->quantities()
                ->with('color')
                ->orderBy('id', 'desc')
                ->get()
                ->groupBy('color.id');
            $newquantities = [];
            $i = 0;
            foreach ($quantities as $key => $quantity) {
                $newquantities[$i]["id"] = $quantity[0]['color']['id'];
                $newquantities[$i]["name"] = $quantity[0]['color']['name'];
                $newquantities[$i]["color_value"] = $quantity[0]['color']['color_value'];
                foreach ($quantity as $item) {
                    $newquantities[$i]['quantities'][] = $item->only(['id', 'size', 'quantity']);
                }
                $i++;
            }
            $product['quantities'] = $newquantities;
            $collection = $product->collection->pluck('products')->toArray();
            if (count($collection) > 0) {
                //reshape product collection producta
                $collection = $product->collection->take(7)->pluck('products')->toArray();

                $newCollectionProducts = array_reduce($collection, 'array_merge', array());

                if (($key = array_search($product->id, array_column($newCollectionProducts, 'id'))) !== false) {
                    unset($newCollectionProducts[$key]);
                }
                $tempArray = array_unique(array_column($newCollectionProducts, 'id'));
                $moreUniqueArray = array_values(array_intersect_key($newCollectionProducts, $tempArray));
                unset($product['subCategory']);
                unset($product['collection']);
                $product['collection'] = $moreUniqueArray;
            } else {
                if ($product->store_id != null) {
                    $subCategory = $product['subCategory']['products']->take(7)->toArray();
                    if (($key = array_search($product->id, array_column($subCategory, 'id'))) !== false) {
                        unset($subCategory[$key]);
                    }
                    unset($product['collection']);
                    unset($product['subCategory']);
                    $product['collection'] = $this->unique_multidim_array(array_values($subCategory), 'id');
                } else {
                    $brand = $product['brand']['products']->take(7)->toArray();
                    if (($key = array_search($product->id, array_column($brand, 'id'))) !== false) {
                        unset($brand[$key]);
                    }
                    unset($product['collection']);
                    unset($product['brand']);
                    $product['collection'] = $this->unique_multidim_array(array_values($brand), 'id');
                }
            }
            $favorite = $wishlist = 0;

            try {
                if ($user = JWTAuth::parseToken()->authenticate()) {
                    $favorite = Product::where('id', $product->id)->wherehas('favorites', function ($q) {
                        $q->wherehas('favorite', function ($q) {
                            $q->where('user_id', Auth::id());
                        });
                    })->count();
                    $favorite > 0 ? $favorite = 1 : $favorite = 0;
                }
            } catch (Exception $e) {
            };
            try {
                if ($user = JWTAuth::parseToken()->authenticate()) {
                    $wishlist = Product::where('id', $product->id)->wherehas('WishlistItems', function ($q) {
                        $q->where('user_id', Auth::id());
                    })->count();
                    $wishlist > 0 ? $wishlist = 1 : $wishlist = 0;
                }
            } catch (Exception $e) {
            };

            if (request()->has('token') && request()->token != null) {
                if ($user = JWTAuth::parseToken()->authenticate()) {
                    $favorite = Product::where('id', $product->id)->wherehas('favorites', function ($q) {
                        $q->wherehas('favorite', function ($q) {
                            $q->where('user_id', Auth::id());
                        });
                    })
                        ->count();
                    $favorite > 0 ? $favorite = 1 : $favorite = 0;

                    $wishlist = Product::where('id', $product->id)->wherehas('WishlistItems', function ($q) {
                        $q->where('user_id', Auth::id());
                    })->count();
                    $wishlist > 0 ? $wishlist = 1 : $wishlist = 0;
                }
            }
            if ($product->store_id != null) {
                // dd($product->store->logo);
                $product['logo'] = $product->store->logo;
            } elseif ($product->brand_id != null) {
                $product['logo'] = $product->brand->logo;
            } else {
                $product['logo'] = null;
            }
            $product['favorite'] = $favorite;
            $product['wishlist'] = $wishlist;
        }
        return $product;
    }
}