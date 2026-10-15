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
        Schema::create('bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignUuid('car_id')->constrained('cars');
            $table->date('start_date');
            $table->date('end_date');
            $table->date('actual_end_date')->nullable();
            $table->decimal('original_price', 10, 2);
            $table->decimal('final_price', 10, 2)->nullable();
            $table->text('completion_note')->nullable();
            $table->string('status')->default('created'); // created, cancelled, completed
            $table->string('cancel_reason')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->timestamps();
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
