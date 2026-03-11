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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['waiting', 'active', 'ended', 'reported']);
            $table->timestamps();
            $table->timestamp('ended_at')->nullable();
            $table->integer('room_duration_seconds')->nullable();
            $table->boolean('is_anonymized')->default(true);

            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
