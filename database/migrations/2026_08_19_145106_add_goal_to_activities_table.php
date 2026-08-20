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
    Schema::table('activities', function (Blueprint $table) {
        // Nullable karena kata kamu: "Tidak semua Task harus masuk Goal"
        $table->foreignId('goal_id')->nullable()->constrained()->onDelete('set null');
        $table->foreignId('milestone_id')->nullable()->constrained()->onDelete('set null');
    });
}

public function down(): void
{
    Schema::table('activities', function (Blueprint $table) {
        $table->dropForeign(['goal_id']);
        $table->dropForeign(['milestone_id']);
        $table->dropColumn(['goal_id', 'milestone_id']);
    });
}
};
