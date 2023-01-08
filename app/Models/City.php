<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Address;
use App\Models\Store;
class City extends Model
{
    use HasFactory;

 /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'price'


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
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
    public function stores()
    {
        return $this->hasMany(Store::class);
    }
}

