<?php

namespace Project\Api\Blog\Likes;

use Project\Api\Blog\Post;
use Project\Api\Blog\User;
use Project\Api\Blog\UUID;

class PostLike implements LikeInterface
{
    /**
     * @param UUID $uuid
     * @param Post $post
     * @param User $user
     */
    public function __construct (
         private UUID $uuid,
         private Post $post,
         private User $user
    )
    {

    }

    public function uuid(): string
    {
        return $this->uuid;
    }

    public function getPostUuid(): string
    {
        return $this->post->uuid();
    }

    public function getUserUuid(): string
    {
        return $this->user->uuid();
    }

    public function __toString(): string{
        return $this->uuid();
    }



}