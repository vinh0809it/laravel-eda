<?php

namespace Src\Shared\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

final class EventStoreModel extends Model
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
