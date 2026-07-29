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
        Schema::create('projects', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('number');
            $table->string('title');
            $table->string('category');
            $table->string('year');
            $table->text('description');
            $table->json('technologies');
            $table->string('image_bg')->default('from-emerald-900/40 via-neutral-900 to-black');
            $table->string('accent_color')->default('#B8FF00');
            $table->string('link')->nullable();
            $table->boolean('featured')->default(true);
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
