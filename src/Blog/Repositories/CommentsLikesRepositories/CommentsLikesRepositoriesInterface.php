<?php

namespace Project\Api\Blog\Repositories\CommentsLikesRepositories;


use Project\Api\Blog\Likes\CommentLike;
use Project\Api\Blog\UUID;

interface CommentsLikesRepositoriesInterface
{
    public function save(CommentLike $commentLike);

    public function getByCommentsUuid(UUID $commentUuid);

}