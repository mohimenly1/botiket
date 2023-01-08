<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavItem extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fav_id',
        'product_id',
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
    public function favorite()
    {
        return $this->belongsTo(Favorite::class, 'fav_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
}
