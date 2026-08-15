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
        Schema::table('tasks', function (Blueprint $table) {
            // Menambahkan kolom untuk menyimpan rincian Work Log dalam format JSON
            $table->json('work_logs')->nullable()->after('status');
            
            // Menambahkan kolom untuk menyimpan total menit pengerjaan agar mudah dihitung
            $table->integer('actual_time')->default(0)->after('effort_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['work_logs', 'actual_time']);
        });
    }
};