<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EModulChatLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'e_modul_id',
        'student_name',
        'page_number',
        'question',
        'answer',
        'ip_address',
        'user_agent',
    ];

    /**
     * Get the e-modul that owns the chat log.
     */
    public function modul(): BelongsTo
    {
        return $this->belongsTo(EModul::class, 'e_modul_id');
    }
}
