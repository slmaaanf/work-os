<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_plans', function (Blueprint $table) {
            $table->timestamp('clock_in_at')->nullable()->after('date');
            $table->timestamp('clock_out_at')->nullable()->after('clock_in_at');
        });
    }

    public function down(): void
    {
        Schema::table('daily_plans', function (Blueprint $table) {
            $table->dropColumn(['clock_in_at', 'clock_out_at']);
        });
    }
};