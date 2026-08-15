<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('stakeholder')->nullable()->after('actual_time');
            $table->string('environment')->nullable()->after('stakeholder');
            $table->text('daily_win')->nullable()->after('environment');
            $table->text('oops_moment')->nullable()->after('daily_win');
            $table->text('lesson_learned')->nullable()->after('oops_moment');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['stakeholder', 'environment', 'daily_win', 'oops_moment', 'lesson_learned']);
        });
    }
};