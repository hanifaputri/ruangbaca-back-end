<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // php artisan migrate:refresh --path=/database/migrations/2021_12_16_103520_create_transactions.php
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('book_id')->constrained('books');
            $table->foreignId('user_id')->constrained('users');
            $table->integer('duration');
            $table->enum('status',['Sedang Dipinjam', 'Selesai', 'Terlambat']);
            $table->timestamp('borrowed_at')->nullable();
            $table->timestamp('deadline')->nullable();
            $table->timestamp('returned_at')->nullable();

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
        Schema::dropIfExists('transactions');
    }
}
