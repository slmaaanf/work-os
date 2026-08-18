<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_plan_activities', function (Blueprint $table) {
            $table->id();

            $table->foreignId('daily_plan_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('activity_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedInteger('planned_mins');

            $table->enum('status', [
                'planned',
                'in_progress',
                'done_today',
            ])->default('planned');

            $table->boolean('is_carried_over')->default(false);

            $table->foreignId('carried_from_id')
                ->nullable()
                ->constrained('daily_plan_activities')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(['daily_plan_id', 'activity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_plan_activities');
    }
};