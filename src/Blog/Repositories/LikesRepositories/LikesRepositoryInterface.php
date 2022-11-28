<?php

namespace Project\Api\Blog\Repositories\LikesRepositories;

use Project\Api\Blog\Likes\LikeInterface;
use Project\Api\Blog\UUID;

interface LikesRepositoryInterface
{
    public function save(LikeInterface $like);

    public function getByPostUuid(UUID $uuid);
}