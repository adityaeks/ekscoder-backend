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
        Schema::create('vps_metrics_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vps_server_id')->constrained('vps_servers')->onDelete('cascade');
            $table->float('cpu_usage')->default(0);
            $table->bigInteger('ram_used_mb')->default(0);
            $table->bigInteger('ram_total_mb')->default(0);
            $table->float('disk_used_gb')->default(0);
            $table->float('disk_total_gb')->default(0);
            $table->float('load_avg_1m')->default(0);
            $table->bigInteger('uptime_seconds')->default(0);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vps_metrics_logs');
    }
};
