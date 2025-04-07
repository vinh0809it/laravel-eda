<?php

namespace Src\Infrastructure\Booking\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'id',
        'car_id',
        'user_id',
        'start_date',
        'end_date',
        'total_price',
        'status',
        'version',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'total_price' => 'decimal:2',
    ];
} 