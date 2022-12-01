<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
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
use Project\Api\Http\Auth\IdentificationInterface;
use Project\Api\Http\Auth\JsonBodyUuidIdentification;
use Psr\Log\LoggerInterface;

require_once __DIR__ . '/vendor/autoload.php';

\Dotenv\Dotenv::createImmutable(__DIR__)->safeLoad();

$container = new DIContainer();

$logger = (new Logger('blog'));

if($_SERVER['LOG_TO_FILES'] === 'yes'){
    $logger
        ->pushHandler(new StreamHandler(__DIR__ . '/logs/blog.log'))
        ->pushHandler(new StreamHandler(
            __DIR__ . '/logs/blog.error.log',
            level: Logger::ERROR,
            bubble: false,
        ));
}

if($_SERVER['LOG_TO_CONSOLE']==='yes'){
    $logger
        ->pushHandler(new StreamHandler("php://stdout"));
}


$container->bind(
    PDO::class,
    new PDO ('sqlite:'. __DIR__ . '/' . $_SERVER['SQLITE_DB_PATH'] )
);

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

$container->bind(LoggerInterface::class,
    $logger
);

$container->bind(IdentificationInterface::class,
JsonBodyUuidIdentification::class);

return $container;
