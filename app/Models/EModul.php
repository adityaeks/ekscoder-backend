<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EModul extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'category',
        'file_path',
        'file_size',
        'total_pages',
        'cover_image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'total_pages' => 'integer',
        'file_size' => 'integer',
    ];

    /**
     * Get the full URL to the PDF file.
     */
    public function getPdfUrlAttribute()
    {
        if (!$this->file_path) return null;
        if (str_starts_with($this->file_path, 'http')) {
            return $this->file_path;
        }
        return asset('storage/' . ltrim($this->file_path, '/'));
    }

    /**
     * Get public share URL pointing to main frontend domain (e.g. ekscoder.com/modul/{slug}).
     */
    public function getPublicShareUrlAttribute(): string
    {
        $frontend = rtrim(config('app.frontend_url', env('FRONTEND_URL', 'https://ekscoder.com')), '/');
        return "{$frontend}/modul/{$this->slug}";
    }

    /**
     * Get internal portal URL for previewing the public route.
     */
    public function getPublicInternalUrlAttribute(): string
    {
        return route('public.e-modul.show', $this->slug);
    }

    /**
     * Format file size for human readability.
     */
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return '1 byte';
        } else {
            return '0 bytes';
        }
    }
}
