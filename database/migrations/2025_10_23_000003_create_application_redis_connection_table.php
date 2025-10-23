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
        Schema::create('application_redis_connection', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('application_id');
            $table->unsignedBigInteger('redis_connection_id');
            $table->boolean('is_primary')->default(false);
            $table->unsignedTinyInteger('priority')->default(10);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            // Ensure unique combination of application and redis connection
            $table->unique(['application_id', 'redis_connection_id'], 'app_redis_conn_unique');

            // Indexes for performance
            $table->index(['application_id', 'is_active']);
            $table->index(['redis_connection_id', 'is_active']);
            $table->index(['is_primary', 'priority']);
            $table->index('last_used_at');

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
        Schema::dropIfExists('application_redis_connection');
    }
};
