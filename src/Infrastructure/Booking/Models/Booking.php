<?php

namespace Src\Infrastructure\Booking\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;
    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Database\Factories\BookingFactory::new();
    }

    protected $fillable = [
        'id',
        'car_id',
        'user_id',
        'start_date',
        'end_date',
        'original_price',
        'status',
        'version',
    ];

    protected $casts = [
        'id' => 'string',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'original_price' => 'decimal:2',
    ];
} 