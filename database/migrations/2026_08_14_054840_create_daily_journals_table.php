<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_journals', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel users
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');
                  
            $table->date('log_date');
            
            // Mode: kerja (Cimory) atau personal
            $table->enum('mode', ['work', 'personal'])->default('work');
            
            $table->time('clock_in')->nullable();
            $table->time('clock_out')->nullable();
            
            // Lokasi (misal: 'Kantor', 'Fore Coffee', 'Rumah')
            $table->string('location')->nullable();
            
            // Caffeine tracker
            $table->integer('caffeine_cups')->default(0);
            
            // Jurnal refleksi
            $table->text('daily_win')->nullable();
            $table->text('oops_moment')->nullable();
            
            $table->timestamps();
            
            // Index untuk mempercepat query Monthly Recap
            $table->index(['user_id', 'log_date', 'mode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_journals');
    }
};