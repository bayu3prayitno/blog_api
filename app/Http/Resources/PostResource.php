<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'article_id' => $this->id,
            'headline' => $this->title,
            'body_text' => $this->content,
            'is_published' => $this->status === 'published' ? true : false,
            'author_reference' => $this->user_id,
            'links' => [
                'self' => route('posts.show', $this->id)
            ]
        ];
    }
}