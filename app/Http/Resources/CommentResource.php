<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'comment_id'          => $this->id,
            'feedback_text'       => $this->comment,
            'related_article_id'  => $this->post_id,
            'commenter_reference' => $this->user_id,
        ];
    }
}
