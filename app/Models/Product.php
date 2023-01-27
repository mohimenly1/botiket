<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ProductMedia;
use App\Models\Brand;
use App\Models\Gender;
use App\Models\Offer;
use App\Models\Category;
use App\Models\Currency;

use App\Models\SubCategory;
use App\Models\Store;
use App\Models\Quantity;



use stdClass;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'store_id',
        'sku',
        'OEM',
        'title',
        'url',
        'description',
        'price',
        'original_price',
        'discount_price',
        'is_shipped',
        'is_featured',
        'brand_id',
        'category_id',
        'sub_category_id',
        'offer_id',
        'gender_id',
        'is_archived',
        'is_new',

        'season'
    ];
    // protected $appends = ['mediasFirst'];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at',
        'pivot'

    ];
    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */

    function highlight()
    {
        return $this->hasMany(Highlight::class);
    }
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
    protected $appends = ['new_price', 'currency', 'price_usd', 'old_price_usd'];

    // public function scopeWithAndWhereHas($query, $relation, $constraint)
    // {
    //     return $query->whereHas($relation, $constraint)
    //         ->with([$relation => $constraint]);
    // }

    public function scopeFilter($query, array $search)
    {



        if (isset($search['color'])) {
            //  dd($search['color']);
            $query->whereHas('quantities', function ($q) use ($search) {
                $q->where('color_id', $search['color']);
            });
        }
        if (isset($search['size'])) {
            $query->whereHas('quantities', function ($q) use ($search) {
                $q->where('size', 'like', '%' . $search['size'] . '%');
            });
        }

        if (isset($search['brand'])) {
            $query->where('brand_id', $search['brand']);
        }

        if (isset($search['from_price']) && isset($search['to_price'])) {
            $query->whereBetween('price', [$search['from_price'], $search['to_price']]);
        }


        if (isset($search['category_id'])) {
            $query->where('category_id', $search['category_id']);
        }
        if (isset($search['gender_id'])) {
            $query->where('gender_id', $search['gender_id']);
        }

        if (isset($search['sub_category_id'])) {
            $query->where('sub_category_id', $search['sub_category_id']);
        }


        // if (isset($search['price'])) {
        //     $query->where('price', '<=', $search['price']);
        // }



        // if (isset($search['price'])) {
        //     $query->where('price', '<=', $search['price']);
        // }

        if (isset($search['only_discounted'])) {
            if ($search['only_discounted'] == 1) {
                $query->where('discount_price', '!=', NULL);
            }
        }


        if (isset($search['sku'])) {


            $query->where('sku', '=',  $search['sku']);
        }
        if (isset($search['title'])) {
            $query->where('title', 'like', '%' . $search['title'] . '%');
        }

        if (isset($search['new'])) {
            $query->where('is_new', 1);
        }

        if (isset($search['season'])) {

            // dd($search['season']);
            $query->where('season', '=', $search['season']);
        }



        // if (isset($search['products_by_store_season'])) {
        //     $store_id = Store::where('id', $search['products_by_store_season'])->first()->season;
        //     // dd($season_store);
        //     $query->where('season', $store_id);
        // }




        // if (isset($search['store'])) {

        //     $query->where('store_id', $search['store']);
        // }
        return $query;
    }
    /**

     * Get the Media for the blog Product.

     */

    public function medias()
    {
        return $this->hasMany(ProductMedia::class);
    }
    public function firstMedia()
    {
        return $this->hasOne(ProductMedia::class)->oldest();
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function gender()
    {
        return $this->belongsTo(Gender::class);
    }
    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function quantities()
    {
        return $this->hasMany(Quantity::class);
    }
    public function collection()
    {
        return $this->belongsToMany(Collection::class, 'collection_items');
    }

    public function cartusers()
    {
        return $this->belongsToMany(User::class, 'cart_items');
    }
    public function favorites()
    {
        return $this->hasMany(FavItem::class);
    }
    public function WishlistItems()
    {
        return $this->hasMany(WishlistItem::class);
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class)->withTrashed();
    }
    public function color()
    {
        return $this->hasMany(Color::class);
    }



    public function getNewPriceAttribute()
    {

        $new_price = null;
        if ($this->offer_id != null) {
            $offer = Offer::where('id', $this->offer_id)->whereNull('deleted_at')->get()->first();
            if ($offer) {

                if ($offer != null && $offer->is_percentage === 1) {

                    $descount_value = ($this->discount_price * $offer->value) / 100;
                    $new_price = round($this->discount_price - $descount_value, 3);
                } else {
                    $new_price = round($this->discount_price - $offer->value, 3);
                }
            }
        } elseif ($this->discount_price != $this->price && $this->discount_price != 0) {
            $new_price = $this->discount_price;
        }
        return $new_price != null ? ceil($new_price) : $new_price;
    }



    public function getPriceUsdAttribute()
    {

        $price = $this->new_price ? $this->new_price : $this->price;

        if ($this->brand_id) {
            $selling_id = Brand::find($this->brand_id)->selling_currency_id;
            $Currency = Currency::find($selling_id);
            return ($price * $Currency->rate);
        } else {
            return ($price * Currency::find(69)->rate);
        }
    }
    public function getOldPriceUsdAttribute()
    {
        $price = $this->price;

        if ($this->brand_id) {
            $selling_id = Brand::find($this->brand_id)->selling_currency_id;
            $Currency = Currency::find($selling_id);
            return ($price * $Currency->rate);
        } else {
            return ($price * Currency::find(69)->rate);
        }
    }



    public function getCurrencyAttribute()
    {
        if ($this->brand_id) {
            $selling_id = Brand::find($this->brand_id)->selling_currency_id;
            $Currency = Currency::find($selling_id);
            return $Currency;
        } else {
            return Currency::find(69);
        }
    }
}