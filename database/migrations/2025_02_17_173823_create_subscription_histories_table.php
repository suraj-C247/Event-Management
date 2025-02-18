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
        Schema::create('subscription_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('plan_name', length: 100); 
            $table->decimal('plan_price', 8, 2); 
            $table->enum('plan_type', ['day', 'week', 'month', 'year']);
            $table->mediumInteger('plan_duration');
            $table->mediumInteger('max_events');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->string('stripe_session_id')->nullable();
            $table->string('stripe_subscription_id')->nullable();
            $table->string('status', length: 50)->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_histories');
    }
};
