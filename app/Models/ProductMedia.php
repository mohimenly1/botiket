<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\Product;
use Illuminate\Support\Facades\URL;

class ProductMedia extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'path',
        'product_id',
        'color_id'
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
    /**

     * Get the Product that owns the Media.

     */
    public function getPathAttribute()
    {
        $is_zara = str_contains($this->attributes['path'],"zara");
        $is_link = str_contains($this->attributes['path'],"http");

        if($is_zara||$is_link){
            $image=$this->attributes['path'];
        }else{
            $image=URL::to('/').$this->attributes['path'];
        }
        return $image;
    }
    public function Product()
    {
        return $this->belongsTo(Product::class);
    }
    public function color()
    {
        return $this->belongsTo(Color::class);
    }
}
