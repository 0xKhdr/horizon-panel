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
        Schema::create('redis_connections', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('host');
            $table->unsignedInteger('port')->default(6379);
            $table->text('password')->nullable(); // Encrypted via property hook
            $table->unsignedTinyInteger('database')->default(0);
            $table->json('options')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('health_status', 20)->nullable();
            $table->timestamp('last_health_check_at')->nullable();
            $table->text('last_error')->nullable();
            $table->string('environment', 50)->default('production');
            $table->string('region', 50)->nullable();
            $table->string('provider', 50)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['is_active', 'health_status']);
            $table->index(['environment', 'is_active']);
            $table->index('last_health_check_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('redis_connections');
    }
};
