<?php

use Project\Api\Blog\Container\DIContainer;
use Project\Api\Blog\Repositories\CommentsLikesRepositories\CommentsLikesRepositoriesInterface;
use Project\Api\Blog\Repositories\CommentsLikesRepositories\SqliteCommentsLikesRepository;
use Project\Api\Blog\Repositories\CommentsRepositories\CommentsRepositoriesInterface;
use Project\Api\Blog\Repositories\CommentsRepositories\SqliteCommentsRepository;
use Project\Api\Blog\Repositories\PostsLikesRepositories\PostsLikesRepositoryInterface;
use Project\Api\Blog\Repositories\PostsLikesRepositories\SqlitePostsLikesRepository;
use Project\Api\Blog\Repositories\PostsRepositories\PostsRepositoryInterface;
use Project\Api\Blog\Repositories\PostsRepositories\SqlitePostsRepository;
use Project\Api\Blog\Repositories\UsersRepositories\SqliteUsersRepository;
use Project\Api\Blog\Repositories\UsersRepositories\UsersRepositoryInterface;

require_once __DIR__ . '/vendor/autoload.php';

$container = new DIContainer();

$container->bind(
    PDO::class,
    new PDO ('sqlite:'. __DIR__ .'/blog.sqlite'));

$container->bind(
    PostsRepositoryInterface::class,
   SqlitePostsRepository::class
);

$container->bind(
    UsersRepositoryInterface::class,
    SqliteUsersRepository::class
);

$container->bind(
    CommentsRepositoriesInterface::class,
    SqliteCommentsRepository::class
);

$container->bind(
   PostsLikesRepositoryInterface::class,
   SqlitePostsLikesRepository::class
);

$container->bind(
    CommentsLikesRepositoriesInterface::class,
    SqliteCommentsLikesRepository::class
);

return $container;
