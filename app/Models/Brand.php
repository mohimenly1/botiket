<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\URL;
use App\Models\Currency;


class Brand extends Model
{

    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'logo',
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


    protected $appends = ['original_currency', 'selling_currency'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function originalCurrency()
    {
        return $this->belongsTo(Currency::class);
    }
    public function sellingCurrency()
    {
        return $this->belongsTo(Currency::class);
    }




    public function getOriginalCurrencyAttribute()
    {
        return Currency::find($this->original_currency_id);  
    }

    public function getSellingCurrencyAttribute()
    {
        return Currency::find($this->selling_currency_id);
    }


}
