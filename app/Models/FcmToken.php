<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FcmToken extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'fcm_token',
       'user_id'
      
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



}
