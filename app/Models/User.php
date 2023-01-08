<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\Store;
use App\Models\FcmToken;
use App\Models\Address;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\URL;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'image',
        'password',
        'phone',
        'store_id',
        'role',
        'is_active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'updated_at',
        'deleted_at',
        'pivot'
    ];
    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
    public function getImageAttribute()
    {
        if ($this->attributes['image'] == null) {
            return null;
        } else {
            $image = URL::to('/') . $this->attributes['image'];
            return $image;
        }
    }

    public function scopeWithAndWhereHas($query, $relation, $constraint)
    {
        return $query->whereHas($relation, $constraint)
            ->with([$relation => $constraint]);
    }
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }



    public function store()
    {
        return $this->belongsToMany(Store::class, 'store_user');
    }
    public function followedStores()
    {
        return $this->belongsToMany(Store::class, 'followed_stores');
    }

    public function fcm()
    {
        return $this->hasMany(FcmToken::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }
    public function directFavorites()
    {
        return $this->belongsToMany(User::class,);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function address()
    {
        return $this->hasMany(Address::class);
    }

    public function cartproducts()
    {
        return $this->belongsToMany(Product::class, 'cart_items');
    }
    
    public function WishlistItems()
    {
        return $this->hasMany(WishlistItem::class);
    }
}
