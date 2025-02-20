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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', length: 100); 
            $table->decimal('price', 8, 2); 
            $table->enum('type', ['day', 'week', 'month', 'year']);
            $table->smallInteger('duration');
            $table->text('description');
            $table->smallInteger('max_events');
            $table->boolean('is_active')->default(true);
            $table->string('stripe_price_id', length: 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
