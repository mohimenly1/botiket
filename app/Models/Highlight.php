<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Highlight extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'product_id'

    ];

    function product()
    {
        return $this->belongsTo(Product::class);
    }
}