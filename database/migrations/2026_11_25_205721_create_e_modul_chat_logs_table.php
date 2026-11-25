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
        Schema::create('e_modul_chat_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('e_modul_id')->constrained('e_moduls')->cascadeOnDelete();
            $table->string('student_name');
            $table->string('page_number')->nullable();
            $table->text('question');
            $table->text('answer');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['e_modul_id', 'student_name']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('e_modul_chat_logs');
    }
};
