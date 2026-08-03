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
        Schema::create('vps_servers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('ip_address')->nullable();
            $table->string('auth_token')->unique();
            $table->enum('status', ['online', 'warning', 'offline'])->default('offline');
            $table->integer('check_interval')->default(5); // in minutes
            $table->timestamp('last_ping_at')->nullable();
            $table->string('os_info')->nullable();
            $table->integer('cpu_cores')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vps_servers');
    }
};
