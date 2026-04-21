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
        Schema::create('comments', function (Blueprint $table) {
        $table->integer('id')->autoIncrement();
        $table->string('comment', 250);
        $table->integer('post_id');
        $table->integer('user_id');

        $table->foreign('post_id')
              ->references('id')
              ->on('posts')
              ->onDelete('restrict')
              ->onUpdate('restrict');

        $table->foreign('user_id')
              ->references('id')
              ->on('users')
              ->onDelete('restrict')
              ->onUpdate('restrict');

        $table->engine = 'InnoDB';
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
