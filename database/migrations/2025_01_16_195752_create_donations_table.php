<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id('donation_id');
            $table->unsignedBigInteger('user_id'); // Not a foreign key
            $table->date('donation_date');
            $table->integer('quantity');
            $table->string('donation_center');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->integer('credit_point');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('donations');
    }
};
