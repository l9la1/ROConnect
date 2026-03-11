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
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('display_name', 50)->default('Guest');
            $table->string('preferred_language', 10)->default('en');
            $table->text('avatar_url')->nullable();
            $table->string('interest_tag', 50)->nullable()->comment('Optional interest for matching');
            $table->timestamps();
            $table->timestamp('last_active')->useCurrent();

            $table->index('last_active');
            $table->index('preferred_language');
            $table->index('interest_tag');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
    }
};
