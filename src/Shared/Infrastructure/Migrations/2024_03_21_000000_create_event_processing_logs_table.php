<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_processing_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('event_id');
            $table->string('listener');
            $table->enum('status', ['success', 'failed']);
            $table->text('error')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['event_id', 'listener']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_processing_logs');
    }
}; 