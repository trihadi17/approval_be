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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('id_card',16)->unique();
            $table->string('name',50);
            $table->string('email',100)->unique();
            $table->string('password');
            $table->string('address',100);
            $table->enum('gender',['Male','Female']);
            $table->string('phone_number',15);
            $table->string('profile_picture', 255)->default('profile.jpg');
            $table->enum('role',['Admin','Verifikator','Ordinary'])->default('Ordinary');
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
