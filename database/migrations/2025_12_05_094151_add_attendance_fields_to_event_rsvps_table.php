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
        Schema::table('event_rsvps', function (Blueprint $table) {
            $table->boolean('attended')->default(false)->after('children_count');
            $table->timestamp('attendance_timestamp')->nullable()->after('attended');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_rsvps', function (Blueprint $table) {
            $table->dropColumn(['attended', 'attendance_timestamp']);
        });
    }
};