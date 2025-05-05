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
        // Create users table
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('full_name'); // Replacing first_name and last_name with full_name
            $table->string('username')->unique(); // Added username field and set as unique
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone_number')->nullable();
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('current_latitude', 10, 8)->nullable(); // Added current latitude
            $table->decimal('current_longitude', 11, 8)->nullable(); // Added current longitude
            $table->date('date_of_birth')->nullable();
            $table->integer('age')->nullable();
            $table->string('blood_type', 3)->nullable();
            $table->date('last_donation_date')->nullable();
            $table->boolean('eligibility_status')->nullable(true);
            $table->integer('credit_points')->default(0);
            $table->string('token')->nullable();
            $table->string('user_type')->default('user');
            $table->string('status')->default('pending');
            $table->integer('count')->default(0);
            $table->string('otp')->nullable(); // Added OTP field
            $table->string('donor_type')->nullable(); // Added donor type field
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
            $table->integer('count')->default(0);
        });

        Schema::create('credit_transactions', function (Blueprint $table) {
            $table->id('transaction_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('credit_change');
            $table->string('transaction_type');
            $table->text('description')->nullable();
            $table->timestamp('transaction_date');
            $table->integer('count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_transactions');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
