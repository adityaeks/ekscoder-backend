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
        Schema::create('monitored_sites', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->integer('check_interval')->default(5); // in minutes
            $table->enum('status', ['up', 'down', 'unknown'])->default('unknown');
            $table->integer('last_status_code')->nullable();
            $table->integer('last_response_time')->nullable(); // in ms
            $table->timestamp('last_checked_at')->nullable();
            $table->string('ssl_status')->nullable(); // valid, expired, invalid, none
            $table->timestamp('ssl_expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('site_check_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monitored_site_id')->constrained('monitored_sites')->onDelete('cascade');
            $table->enum('status', ['up', 'down']);
            $table->integer('status_code')->nullable();
            $table->integer('response_time')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('checked_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_check_logs');
        Schema::dropIfExists('monitored_sites');
    }
};
