<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Invoices;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\QueryBuilder;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'delivery_price',
        'user_id',
        'store_id',
        'order_status_id',
        'total_amount',
        'discount',
        'delivery_date',
        'delivery_id',
        'address_id'
    ];
    // protected $appends = ['firstMedia'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at',
    ];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
    public function scopeWithAndWhereHas($query, $relation, $constraint)
    {
        return $query->whereHas($relation, $constraint)
            ->with([$relation => $constraint]);
    }
    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
    public function statu()
    {
        return $this->belongsTo(OrderStatu::class, 'order_status_id');
    }
    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }
    public function address()
    {
        return $this->belongsTo(Address::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function directItems()
    {
        return $this->belongsToMany(Product::class, 'order_items');
    }
    public function firstItem()
    {
        return $this->belongsToMany(Product::class, 'order_items')->whereHas('firstMedia')->oldest();
    }
    public function invoice()
    {
        return $this->hasOne(Invoices::class);
    }
}
