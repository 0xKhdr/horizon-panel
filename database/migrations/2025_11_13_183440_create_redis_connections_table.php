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
            $table->timestamps();
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
