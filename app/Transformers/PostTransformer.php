<?php

namespace App\Transformers;

use App\Models\Post;
use League\Fractal\TransformerAbstract;

class PostTransformer extends TransformerAbstract
{
    /**
     * Mentransformasi data model Post menjadi array JSON yang terstruktur.
     */
    public function transform(Post $post): array
    {
        return [
            'id'        => $post->id, 
            'title'     => $post->title, 
            'status'    => $post->status,
            'content'   => $post->content,
            'image_url' => $post->image ? asset('storage/' . $post->image) : null,
            
            'user_id'   => $post->user_id,
        ];
    }
}