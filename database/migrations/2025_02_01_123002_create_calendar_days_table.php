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
        Schema::create('calendar_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calendar_id')->index()->constrained('calendars');
            $table->unsignedTinyInteger('month_day_id');
            $table->time('work_start');
            $table->time('work_end');
            $table->time('lunch_start')->nullable();
            $table->time('lunch_end')->nullable();
            $table->time('control_start')->nullable();
            $table->time('control_end')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_days');
    }
};
