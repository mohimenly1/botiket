<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubCategory extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'category_id',
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
    public function Category()
    {
        return $this->belongsTo(Category::class);
    }
    public function products()
    {
        return $this->hasMany(Product::class,'sub_category_id');
    }

    public function percentage()
    {
        return $this->hasOne(Percentage::class);
    }

}
