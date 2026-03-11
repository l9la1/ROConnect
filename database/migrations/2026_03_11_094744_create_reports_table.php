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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->foreignId('reporter_id')->constrained('user_sessions')->cascadeOnDelete();
            $table->enum('reason', [
                'inappropriate_behavior',
                'hate_speech',
                'technical_issue',
            ]);
            $table->text('details')->nullable();
            $table->timestamps();
            $table->boolean('resolved')->default(false);

            $table->index('room_id');
            $table->index('reporter_id');
            $table->index('resolved');
            $table->index('reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
