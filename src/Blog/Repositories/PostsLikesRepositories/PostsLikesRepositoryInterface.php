<?php

namespace Project\Api\Blog\Repositories\PostsLikesRepositories;

use Project\Api\Blog\Likes\PostLike;
use Project\Api\Blog\UUID;

interface PostsLikesRepositoryInterface
{
    public function save(PostLike $like);

    public function getByPostUuid(UUID $postUuid);
}