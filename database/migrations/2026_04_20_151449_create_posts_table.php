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
        Schema::create('posts', function (Blueprint $table) {
        $table->integer('id')->autoIncrement();
        $table->string('title', 100);
        $table->enum('status', ['draft', 'published'])->default('draft');
        $table->text('content');
        $table->integer('user_id');

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
        Schema::dropIfExists('posts');
    }
};
