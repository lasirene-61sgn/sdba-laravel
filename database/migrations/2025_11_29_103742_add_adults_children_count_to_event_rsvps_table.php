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
            $table->unsignedInteger('adults_count')->default(0);
            $table->unsignedInteger('children_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_rsvps', function (Blueprint $table) {
            $table->dropColumn(['adults_count', 'children_count']);
        });
    }
};
