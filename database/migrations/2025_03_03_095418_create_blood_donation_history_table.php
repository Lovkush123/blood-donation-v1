<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('blood_donation_history', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id'); // Changed to integer, removed foreign key
            $table->integer('doctor_id'); // Changed to integer, removed foreign key
            $table->date('dod'); // Date of donation
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('blood_donation_history');
    }
};
