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
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['post_id']);
            $table->foreign('post_id')
                ->references('id')->on('posts')
                ->onDelete('cascade')
                ->change();
            $table->dropForeign(['parent_id']);
            $table->foreign('parent_id')
                ->references('id')->on('comments')
                ->onDelete('cascade')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['post_id']);
            $table->foreign('post_id')
                ->references('id')->on('posts')
                ->onDelete('restrict')
                ->change();
            $table->dropForeign(['parent_id']);
            $table->foreign('parent_id')
                ->references('id')->on('comments')
                ->onDelete('restrict')
                ->change();
        });
    }
};
