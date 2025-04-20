<?php

declare(strict_types=1);

namespace Src\Infrastructure\Shared\Loggers\Models;

use Illuminate\Database\Eloquent\Model;

class EventProcessingLog extends Model
{
    protected $table = 'event_processing_logs';

    protected $fillable = [
        'event_id',
        'listener',
        'status',
        'error',
        'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];
} 