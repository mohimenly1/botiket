<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoices extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'total_paid_amount ',
        'order_id',
        'coupon_id',
        'status',
        'discount',
        'rest_amount',
        'payment_method_id',

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
    ];

    protected $appends = ['rest_amount_details'];

    public function scopeWithAndWhereHas($query, $relation, $constraint)
    {
        return $query->whereHas($relation, $constraint)
            ->with([$relation => $constraint]);
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'invoice_id');
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function getRestAmountDetailsAttribute(){

        $total_each_currency = [];
        $order_items = $this->order->items;
        foreach ( $order_items as $key => $item ) {
            $product_currency = $item->product->currency;

            if( array_key_exists($product_currency->base , $total_each_currency) ){
                $total_each_currency[$product_currency->base] += $item->total_amount;
            }else{
                $total_each_currency[$product_currency->base] = 0;
                $total_each_currency[$product_currency->base] += $item->total_amount;
            }
        }
        return $total_each_currency;
    }

}
