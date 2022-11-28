<?php

namespace Project\Api\Blog\Likes;


use Project\Api\Blog\Comment;
use Project\Api\Blog\User;
use Project\Api\Blog\UUID;

class CommentLike
{
    public function __construct (
        private UUID $uuid,
        private Comment $comment,
        private User $user
    )
    {

    }

    /**
     * @return string
     */
    public function uuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getCommentUuid(): string
    {
        return $this->comment->uuid();
    }

    /**
     * @return string
     */
    public function getUserUuid(): string
    {
        return $this->user->uuid();
    }

    public function __toString(): string
    {
        return $this->uuid();
    }


}