<?php

namespace App\Transformers;

use App\Models\Comment;
use League\Fractal\TransformerAbstract;

class CommentTransformer extends TransformerAbstract
{
    public function transform(Comment $comment): array
    {
        return [
            'id'      => $comment->id,
            'comment' => $comment->comment,
            'post_id' => $comment->post_id,
            'user_id' => $comment->user_id,
        ];
    }
}
