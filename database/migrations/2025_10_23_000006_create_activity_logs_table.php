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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('log_name')->nullable();
            $table->text('description');
            $table->nullableMorphs('subject', 'activity_subject');
            $table->nullableMorphs('causer', 'activity_causer');
            $table->json('properties')->nullable();
            $table->string('event', 255)->nullable();
            $table->timestamp('created_at')->nullable();

            // Indexes for performance
            $table->index(['log_name', 'created_at']);
            $table->index(['causer_type', 'causer_id']);
            $table->index(['subject_type', 'subject_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
