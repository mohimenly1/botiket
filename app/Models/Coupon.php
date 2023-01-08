<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'is_percentage',
        'value',
        'usage_count',
        'store_id',
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
    protected $appends = ['status'];
    public function getStatusAttribute()
    {
        if ($this->attributes['usage_count']>0) {
            return 'true';
        } else {
            return 'false';
        }
    }


}
