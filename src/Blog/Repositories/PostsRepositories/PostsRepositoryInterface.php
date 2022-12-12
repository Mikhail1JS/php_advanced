<?php

namespace Project\Api\Blog\Repositories\PostsRepositories;

use Project\Api\Blog\Post;
use Project\Api\Blog\UUID;

interface PostsRepositoryInterface
{
    public function save(Post $post): void ;
    public function get(UUID $uuid): Post;
    public function delete(UUID $uuid): void;
    public function getByTitle(string $title): Post;
}