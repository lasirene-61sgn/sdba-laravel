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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('email')->nullable()->after('whatsapp');
            $table->integer('age')->nullable()->after('email');
            $table->string('gender')->nullable()->after('age');
            $table->string('business_type')->nullable()->after('gender');
            $table->string('product_service')->nullable()->after('business_type');
            $table->text('office_address')->nullable()->after('product_service');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['email', 'age', 'gender', 'business_type', 'product_service', 'office_address']);
        });
    }
};
