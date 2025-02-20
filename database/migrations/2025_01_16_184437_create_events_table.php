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
        Schema::create('events', function (Blueprint $table) {
            $table->id('event_id'); // Primary Key
            $table->string('event_name');
            $table->string('event_type');
            $table->date('event_date');
            $table->string('location');
            $table->string('organizer');
            $table->text('description')->nullable();
            $table->string('status')->default('pending'); // Default value
            $table->unsignedBigInteger('user_id'); // No foreign key
            $table->integer('members')->default(0); // New field for number of members
            $table->string('concern_person'); // New field for the concerned person
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
};
