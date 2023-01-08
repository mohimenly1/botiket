<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Collection extends Model
{
    use HasFactory,SoftDeletes;
    /**
        * The attributes that are mass assignable.
        *
        * @var array
        */
    protected $fillable = [
        'description',
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
        'pivot'
    ];

   
    public function products()
    {
        return $this->belongsToMany(Product::class,'collection_items');
    }
    public function firstProductsMedia()
    {
        return $this->belongsToMany(Product::class,'collection_items')->limit(1)->with('firstMedia');
    }
}