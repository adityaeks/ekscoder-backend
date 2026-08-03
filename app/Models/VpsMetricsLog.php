<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VpsMetricsLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'vps_server_id',
        'cpu_usage',
        'ram_used_mb',
        'ram_total_mb',
        'disk_used_gb',
        'disk_total_gb',
        'load_avg_1m',
        'uptime_seconds',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Get the VPS server that owns this log.
     */
    public function server()
    {
        return $this->belongsTo(VpsServer::class, 'vps_server_id');
    }
}
