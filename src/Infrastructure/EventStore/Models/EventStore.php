<?php

namespace Src\Infrastructure\EventStore\Models;

use Illuminate\Database\Eloquent\Model;

class EventStore extends Model
{
    protected $table = 'event_store';

    protected $fillable = [
        'event_id',
        'event_type',
        'aggregate_type',
        'aggregate_id',
        'event_data',
        'metadata',
        'created_at',
        'version',
    ];

    protected $casts = [
        'event_data' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public $timestamps = false;
}
