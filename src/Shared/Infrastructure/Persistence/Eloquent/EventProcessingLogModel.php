<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

final class EventProcessingLogModel extends Model
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