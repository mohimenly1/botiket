<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Color extends Model
{

    use HasFactory,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'color_value',
    ];

    public function getColorValueAttribute()
    {
        $is_hash = str_contains($this->attributes['color_value'],"#");
        if($is_hash){
            $val=trim($this->attributes['color_value'],'#');
        }else{
            return $this->attributes['color_value'];
        }
        return $val;
    }

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
    public function product()
    {
        return $this->hasMany(Product::class);
    }

}
