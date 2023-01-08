<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\URL;

class Category extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'image',
        'gender_id'
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
    public function scopeFilter($query , $search){
        return $query->where('name','like','%'.$search.'%');
    }
    public function stores()
    {
        return $this->belongsToMany(Store::class, 'store_categories');
    }

    public function getImageAttribute()
    {
        $image=URL::to('/').$this->attributes['image'];
        return $image;
    }
    public function subCategories()
    {
        return $this->hasMany(SubCategory::class);
    }
    public function gender()
    {
        return $this->belongsTo(Gender::class);
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    
}
