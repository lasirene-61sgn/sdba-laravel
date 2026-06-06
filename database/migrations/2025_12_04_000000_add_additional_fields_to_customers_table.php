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
            $table->string('education')->nullable()->after('anniversary_date');
            $table->string('occupation')->nullable()->after('education');
            $table->string('blood_group')->nullable()->after('occupation');
            $table->text('hobbies')->nullable()->after('blood_group');
            $table->string('native_place')->nullable()->after('hobbies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['education', 'occupation', 'blood_group', 'hobbies', 'native_place']);
        });
    }
};