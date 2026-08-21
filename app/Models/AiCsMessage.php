<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiCsMessage extends Model
{
    use HasFactory;

    protected $table = 'ai_cs_messages';

    protected $fillable = [
        'session_id',
        'role',
        'message',
        'ip_address',
        'user_agent',
        'model_used',
    ];

    /**
     * Scope query for a specific session.
     */
    public function scopeForSession($query, string $sessionId)
    {
        return $query->where('session_id', $sessionId)->orderBy('created_at', 'asc');
    }

    /**
     * Scope query to get session-based conversation groups.
     */
    public function scopeLatestSessions($query)
    {
        return $query->select('session_id')
            ->selectRaw('MAX(created_at) as last_activity')
            ->selectRaw('COUNT(*) as total_messages')
            ->selectRaw('MAX(ip_address) as ip_address')
            ->selectRaw('MAX(user_agent) as user_agent')
            ->groupBy('session_id')
            ->orderBy('last_activity', 'desc');
    }
}
