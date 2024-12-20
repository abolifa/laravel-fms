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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('phone')->nullable()->unique();
            $table->string('email')->nullable()->unique();
            $table->string('address')->nullable();
            $table->enum('team', ['akinci', 'tb2', 'other'])->default('other');
            $table->enum('major', ['manager', 'supervisor', 'employee', 'operator', 'payload', 'munition', 'mechanic', 'avionics'])->default('employee');
            $table->date('hiring_date')->nullable();
            $table->date('leaving_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->float('quota')->default(200);
            $table->unsignedBigInteger('original_quota')->default(200);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
