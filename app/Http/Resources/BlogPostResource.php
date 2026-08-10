<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogPostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'cover_image' => $this->cover_image,
            'category' => new BlogCategoryResource($this->whenLoaded('category')),
            'author' => [
                'id' => $this->author?->id,
                'name' => $this->author?->name,
            ],
            'status' => $this->status,
            'featured' => (bool) $this->featured,
            'views_count' => (int) $this->views_count,
            'meta' => [
                'title' => $this->meta_title ?? $this->title,
                'description' => $this->meta_description ?? $this->excerpt,
                'keywords' => $this->meta_keywords,
            ],
            'published_at' => $this->published_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
