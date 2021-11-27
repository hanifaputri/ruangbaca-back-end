<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBooks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // php artisan migrate --path=/database/migrations/2021_11_12_103515_create_books.php
        Schema::create('books', function (Blueprint $table) {
            // This is where you type your code

            $table->id();
            $table->string('title');
            $table->string('description');
            $table->string('author');
            $table->integer('year');
            $table->string('synopsis');
            $table->integer('stock');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('books');
    }
}
