<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'product_id',
        'discount',
        'quantity',
        'quantity_id',
        'color_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        'pivot',

    ];
    protected $appends = ['price', 'new_price','old_price', 'price_usd','old_price_usd','new_price_usd', 'local_currency'];

    public function products()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function relatedQuantity()
    {
        return $this->belongsTo(Quantity::class, 'quantity_id');
    }
    public function users()
    {
        return $this->belongsToMany(User::class);
    }


    public function getLocalCurrencyAttribute(){
        return Currency::find(69);
    }

    public function getPriceAttribute()
    {

        if ($this->discount) {
            return ($this->discount * $this->quantity);
        } else {

            $product = $this->products();
            if( $product->first()->new_price == null ){
                return ceil($product->first()->price * $this->quantity);
            }else{

                return ceil($product->first()->new_price * $this->quantity);
            }

        }
    }
    public function getOldPriceAttribute()
    {

        $product = $this->products();
        return ceil($product->first()->price * $this->quantity);
    }
    public function getNewPriceAttribute()
    {
        $product = $this->products()->first();
        return  ( $product->new_price ? $product->new_price : $product->price ) * $this->quantity;
    }



    public function getPriceUsdAttribute()
    {
        $product = $this->products();

        if ($this->discount) {
            return (($this->discount * $this->quantity) * $product->first()->currency->rate);
        } else {

            if($product->first()->new_price == null){
                return (($product->first()->price * $this->quantity) * $product->first()->currency->rate);
            }else{

                return ( ($product->first()->new_price * $this->quantity)* $product->first()->currency->rate);
            }

        }
    }
    public function getOldPriceUsdAttribute()
    {

        $product = $this->products();
        return (($product->first()->price * $this->quantity) * $product->first()->currency->rate);
    }
    public function getNewPriceUsdAttribute()
    {
        $product = $this->products()->first();
        return ((( $product->new_price ? $product->new_price : $product->price ) * $this->quantity) * $product->first()->currency->rate);
    }




}
