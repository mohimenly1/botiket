<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\URL;

class Store extends Model
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
        'background_image',
        'has_sales',
        'is_store_of_the_week',
        'is_featured',
        'class_a_access',
        'is_active',
        'activated_at',
        'city_id',
        'phone',
        'description',
        'longitude',
        'latitude'

    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at',
        'deleted_at',
        'pivot',
    ];



    public function getLinkAttribute()
    {
        $link = env('DYNAMIC_LINK');;
        return $link;
    }
    public function getLogoAttribute()
    {
        $image = URL::to('/') . $this->attributes['logo'];
        return $image;
    }
    public function getBackgroundImageAttribute()
    {
        if ($this->attributes['background_image']) {
            $image = URL::to('/') . $this->attributes['background_image'];
            return $image;
        } else {
            return null;
        }
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'store_user', 'store_id');
    }
    public function followrs()
    {
        return $this->belongsToMany(User::class, 'followed_stores');
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function offers()
    {
        return $this->hasMany(Offer::class);
    }
    public function genders()
    {
        return $this->belongsToMany(Gender::class, 'store_genders');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'store_categories');
    }
    public function following()
    {
    }
}