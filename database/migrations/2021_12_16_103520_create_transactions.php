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
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('book_id')->constrained('books');
            $table->foreignId('publisher_id')->constrained('publishers');
            $table->foreignId('user_id')->constrained('users');
            $table->date('deadline')->nullable();
            $table->enum('status',['Sedang Dipinjam, Dikembalikan, Terlambat']);
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
        Schema::dropIfExists('transactions');
        $table->dropSoftDeletes();
    }
}
