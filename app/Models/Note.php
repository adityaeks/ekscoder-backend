<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'color',
        'is_pinned',
        'is_archived',
        'labels',
        'sort_order'
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_archived' => 'boolean',
        'labels' => 'array',
    ];

    /**
     * Get the user that owns the note.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Map color identifier to CSS background and border styling.
     */
    public function getColorStyleAttribute(): array
    {
        return match ($this->color) {
            'purple' => [
                'bg' => 'rgba(139, 92, 246, 0.12)',
                'border' => 'rgba(139, 92, 246, 0.35)',
                'accent' => '#a78bfa',
            ],
            'blue' => [
                'bg' => 'rgba(59, 130, 246, 0.12)',
                'border' => 'rgba(59, 130, 246, 0.35)',
                'accent' => '#60a5fa',
            ],
            'green' => [
                'bg' => 'rgba(16, 185, 129, 0.12)',
                'border' => 'rgba(16, 185, 129, 0.35)',
                'accent' => '#34d399',
            ],
            'amber' => [
                'bg' => 'rgba(245, 158, 11, 0.12)',
                'border' => 'rgba(245, 158, 11, 0.35)',
                'accent' => '#fbbf24',
            ],
            'red' => [
                'bg' => 'rgba(239, 68, 68, 0.12)',
                'border' => 'rgba(239, 68, 68, 0.35)',
                'accent' => '#f87171',
            ],
            default => [
                'bg' => 'var(--bg-surface)',
                'border' => 'var(--border)',
                'accent' => 'var(--text-secondary)',
            ],
        };
    }

    /**
     * Parse formatting for display on cards.
     */
    public function getFormattedContentAttribute(): string
    {
        if (empty($this->content)) {
            return '';
        }

        // If content already contains HTML elements (from rich text editor), return it directly
        if (strip_tags($this->content) !== $this->content) {
            return $this->content;
        }

        // Otherwise convert linebreaks for legacy text
        return nl2br(e($this->content));
    }
}


