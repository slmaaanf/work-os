<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');
                  
            $table->date('task_date');
            
            $table->enum('mode', ['work', 'personal'])->default('work');
            
            // Kategori (misal: 'MT Project', 'Hobi', 'Komunitas')
            $table->string('category')->nullable();
            
            $table->string('title');
            
            // Status task
            $table->enum('status', ['todo', 'on_going', 'done', 'blocked'])->default('todo');
            
            // Matrix scoring (Effort vs Impact)
            $table->tinyInteger('effort_score')->default(1)->comment('Scale 1-5');
            $table->tinyInteger('impact_score')->default(1)->comment('Scale 1-5');
            
            // Stakeholder / Blocker Tagging
            $table->string('blocker_tags')->nullable()->comment('Misal: Menunggu Tim Finance');
            
            $table->timestamps();
            
            // Indexing agar pencarian lebih cepat
            $table->index(['user_id', 'task_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};