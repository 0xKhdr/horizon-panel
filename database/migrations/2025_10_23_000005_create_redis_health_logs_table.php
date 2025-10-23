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
        Schema::create('redis_health_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('redis_connection_id');
            $table->string('status', 20);
            $table->decimal('latency_ms', 8, 2)->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedInteger('memory_used_mb')->nullable();
            $table->unsignedInteger('connected_clients')->nullable();
            $table->unsignedBigInteger('keys_count')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('checked_at');
            $table->timestamps();

            // Indexes for performance and querying
            $table->index(['redis_connection_id', 'checked_at']);
            $table->index(['status', 'checked_at']);
            $table->index('checked_at');

            // Add foreign key constraint separately
            $table->foreign('redis_connection_id')->references('id')->on('redis_connections')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('redis_health_logs');
    }
};
