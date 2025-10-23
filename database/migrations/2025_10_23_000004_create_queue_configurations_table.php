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
        Schema::create('queue_configurations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('application_id');
            $table->unsignedBigInteger('redis_connection_id');
            $table->json('queue_names');
            $table->string('balance_strategy', 20)->default('auto');
            $table->unsignedTinyInteger('min_processes')->default(1);
            $table->unsignedTinyInteger('max_processes')->default(10);
            $table->unsignedTinyInteger('tries')->default(3);
            $table->unsignedInteger('timeout')->default(60);
            $table->unsignedInteger('memory')->default(128);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Ensure unique combination of application and redis connection
            $table->unique(['application_id', 'redis_connection_id'], 'queue_config_unique');

            // Indexes for performance
            $table->index(['application_id', 'is_active']);
            $table->index(['redis_connection_id', 'is_active']);
            $table->index('balance_strategy');

            // Add foreign key constraints separately
            $table->foreign('application_id')->references('id')->on('applications')->onDelete('cascade');
            $table->foreign('redis_connection_id')->references('id')->on('redis_connections')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queue_configurations');
    }
};
