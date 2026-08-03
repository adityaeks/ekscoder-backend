<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VpsServer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ip_address',
        'auth_token',
        'status',
        'check_interval',
        'last_ping_at',
        'os_info',
        'cpu_cores',
        'is_active',
    ];

    protected $casts = [
        'last_ping_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get logs associated with this VPS server.
     */
    public function logs()
    {
        return $this->hasMany(VpsMetricsLog::class);
    }

    /**
     * Get latest metrics log.
     */
    public function latestLog()
    {
        return $this->hasOne(VpsMetricsLog::class)->latestOfMany();
    }
}
