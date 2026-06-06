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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('admins')->onDelete('cascade');
            $table->foreignId('village_id')->constrained('villages')->onDelete('cascade');
            $table->string('name');
            $table->string('father_name')->nullable();
            $table->string('gotra')->nullable();
            $table->string('label_name')->nullable();
            $table->string('district')->nullable();
            $table->string('ms_firm_name')->nullable();
            $table->string('dno')->nullable();
            $table->string('street_road')->nullable();
            $table->string('address2')->nullable();
            $table->string('city')->nullable();
            $table->string('pincode')->nullable();
            $table->string('mobile');
            $table->string('whatsapp')->nullable();
            $table->string('state')->nullable();
            $table->enum('status',['active','inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
