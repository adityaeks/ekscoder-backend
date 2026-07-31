<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\LogsActivity;

class Project extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'slug',
        'number',
        'title',
        'category',
        'year',
        'description',
        'technologies',
        'image_bg',
        'accent_color',
        'link',
        'featured',
        'is_active',
        'order',
    ];

    /**
     * Attribute casts.
     */
    protected $casts = [
        'technologies' => 'array',
        'featured' => 'boolean',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Append camelCase attributes to JSON serialization for seamless frontend integration.
     */
    protected $appends = ['imageBg', 'accentColor'];

    public function getImageBgAttribute(): string
    {
        return $this->attributes['image_bg'] ?? '';
    }

    public function getAccentColorAttribute(): string
    {
        return $this->attributes['accent_color'] ?? '';
    }
}
