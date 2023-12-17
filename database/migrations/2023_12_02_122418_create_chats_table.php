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
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('users')->unique();  //контроль уникальности,
            //одна и таже группа пользователей не может создать несколько чатов,
            //группы пользователей должны отличаться по составу
            //если такой контроль не нужен - удалить колонку 'users'
            //если такой контроль нужен только для некторых чатов (например личных), заменить {unique} на {nullable}
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
