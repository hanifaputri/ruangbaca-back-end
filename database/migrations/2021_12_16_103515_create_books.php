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
            $table->bigIncrements('id');
            $table->string('isbn', 15)->unique();
            $table->string('title', 100);
            $table->string('img_url');
            $table->string('author', 50);
            $table->enum('status',['Tersedia', 'Tidak Tersedia']);
            
            $table->foreignId('publisher_id')->constrained('publishers');
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('language_id')->constrained('languages');
            
            $table->timestamps();

            // Enable Soft Deletes
            $table->softDeletes();
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
        $table->dropSoftDeletes();
    }
}
