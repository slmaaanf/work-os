<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('daily_plan_activities', function (Blueprint $table) {
            $table->integer('actual_mins')->default(0)->after('planned_mins');
            $table->text('achievements')->nullable()->after('actual_mins');
            $table->text('blockers')->nullable()->after('achievements');
        });
    }
    public function down(): void {
        Schema::table('daily_plan_activities', function (Blueprint $table) {
            $table->dropColumn(['actual_mins', 'achievements', 'blockers']);
        });
    }
};