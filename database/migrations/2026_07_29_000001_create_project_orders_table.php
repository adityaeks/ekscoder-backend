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
        Schema::create('project_orders', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->string('client_contact')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('budget')->default(0);
            $table->unsignedBigInteger('paid_amount')->default(0);
            $table->string('status')->default('lead'); // lead, requirement, in_progress, review, completed, cancelled
            $table->string('priority')->default('medium'); // low, medium, high
            $table->date('deadline')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_orders');
    }
};
