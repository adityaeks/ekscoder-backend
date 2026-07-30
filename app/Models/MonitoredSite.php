<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoredSite extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'check_interval',
        'status',
        'last_status_code',
        'last_response_time',
        'last_checked_at',
        'ssl_status',
        'ssl_expires_at',
        'is_active',
    ];

    protected $casts = [
        'last_checked_at' => 'datetime',
        'ssl_expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get logs associated with this site.
     */
    public function logs()
    {
        return $this->hasMany(SiteCheckLog::class);
    }
}
