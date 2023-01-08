<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'paid_amount',
        'invoice_id',
        'status',
        'date',
        'reference_number',
    ];
    public function scopeWithAndWhereHas($query, $relation, $constraint)
    {
        return $query->whereHas($relation, $constraint)
            ->with([$relation => $constraint]);
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

    protected $casts = [
        'paid_amount' => 'double',
    ];
    public function invoice()
    {
        return $this->belongsTo(Invoices::class);
    }

    public function getStatusAttribute() {
        return trans('order.'.$this->attributes['status']);
    }
}
