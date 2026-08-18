<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('focus_sessions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('daily_plan_activity_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('status', [
                'active',
                'completed',
                'abandoned',
            ])->default('active');

            $table->timestamp('started_at');
            $table->timestamp('paused_at')->nullable();

            $table->unsignedInteger('accumulated_pause_seconds')
                ->default(0);

            $table->timestamp('finished_at')->nullable();

            $table->unsignedInteger('actual_duration_seconds')
                ->nullable();

            $table->string('result')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('focus_sessions');
    }
};