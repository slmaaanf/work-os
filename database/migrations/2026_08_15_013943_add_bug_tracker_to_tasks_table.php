<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('task_type')->default('Feature')->after('title');
            
            // Kolom khusus Bug Tracker
            $table->string('severity')->nullable()->after('task_type');
            $table->string('bug_environment')->nullable()->after('severity');
            $table->text('steps_to_reproduce')->nullable()->after('bug_environment');
            $table->text('expected_result')->nullable()->after('steps_to_reproduce');
            $table->text('actual_result')->nullable()->after('expected_result');
            $table->text('root_cause')->nullable()->after('actual_result');
            $table->text('solution')->nullable()->after('root_cause');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn([
                'task_type', 'severity', 'bug_environment', 
                'steps_to_reproduce', 'expected_result', 'actual_result', 
                'root_cause', 'solution'
            ]);
        });
    }
};