<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteCheckLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'monitored_site_id',
        'status',
        'status_code',
        'response_time',
        'error_message',
        'checked_at',
    ];

    protected $casts = [
        'checked_at' => 'datetime',
    ];

    /**
     * Get the site associated with this log.
     */
    public function site()
    {
        return $this->belongsTo(MonitoredSite::class, 'monitored_site_id');
    }
}
