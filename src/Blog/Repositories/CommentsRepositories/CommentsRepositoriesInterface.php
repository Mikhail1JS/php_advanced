<?php

namespace Project\Api\Blog\Repositories\CommentsRepositories;

use Project\Api\Blog\Comment;
use Project\Api\Blog\UUID;

interface CommentsRepositoriesInterface
{
    public function save(Comment $comment): void;

    public function get(UUID $uuid): Comment;

}