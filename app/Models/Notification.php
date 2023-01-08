<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
     /**
        * The attributes that are mass assignable.
        *
        * @var array
        */
        protected $fillable = [
            'from_id',
            'to_id',
            'title',
            'body',
            'type',
            'data_id',
            'is_active',
            'is_read',
           
    
        ];
    
        /**
         * The attributes that should be hidden for arrays.
         *
         * @var array
         */
        protected $hidden = [
           
            'updated_at',
            'deleted_at',
        ];
    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];
     
}
