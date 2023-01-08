<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandVisitors extends Model
{
    use HasFactory;
    protected $fillable = [
        'brand_id',
        'counter',
        'created_at',
        'end_date'
    ];
    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];
    public function brand(){
        return $this->belongsTo(Brand::class);
    }
}
