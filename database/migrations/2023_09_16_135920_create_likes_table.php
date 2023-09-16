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
        Schema::create('likes', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('likeable_id');
            $table->string('likeable_type', 150);
            $table->index(['likeable_type', 'likeable_id']);

            $table->foreignId('user_id')->index()->constrained('users')->onDelete('cascade');
            $table->unique(['user_id', 'likeable_type', 'likeable_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};
