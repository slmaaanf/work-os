<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_sessions', function (Blueprint $table) {
            $table->id();
            // Menyambungkan sesi ke task induknya
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            
            // Konsep Timeboxing
            $table->integer('planned_minutes')->default(15);
            $table->integer('actual_minutes')->default(0);
            
            // Log waktu
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            
            // Status sesi timebox
            $table->string('status')->default('planned'); // planned, in_progress, completed
            $table->text('notes')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_sessions');
    }
};