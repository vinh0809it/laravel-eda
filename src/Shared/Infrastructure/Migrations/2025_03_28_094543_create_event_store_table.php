<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('event_store', function (Blueprint $table) {
            $table->id();
            $table->uuid('event_id')->unique();
            $table->string('event_type');
            $table->string('aggregate_type');
            $table->string('aggregate_id');
            $table->json('event_data');
            $table->json('metadata')->nullable();
            $table->timestamp('created_at');
            $table->integer('version');
            
            $table->index(['aggregate_type', 'aggregate_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_store');
    }
};
