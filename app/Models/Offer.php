<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Offer extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'is_percentage',
        'value',
        'expire_date',
        'store_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
