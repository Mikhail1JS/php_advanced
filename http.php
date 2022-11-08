<?php

use Project\Api\Blog\Exceptions\AppException;
use Project\Api\Blog\Exceptions\HttpException;
use Project\Api\Blog\Repositories\UsersRepositories\SqliteUsersRepository;
use Project\Api\Blog\Repositories\PostsRepositories\SqlitePostsRepository;
use Project\Api\Http\Actions\Posts\CreatePosts;
use Project\Api\Http\Actions\Users\FindByUserName;
use Project\Api\Http\ErrorResponse;
use Project\Api\Http\Request;
use Project\Api\Http\SuccessfulResponse;

require_once __DIR__.'/vendor/autoload.php';

$request = new Request($_GET,$_SERVER,file_get_contents('php://input'));

try {
  $path = $request->path();
}catch (HttpException ){
    (new ErrorResponse())->send();
    return;
}

try {
    $method = $request->method();
}catch(HttpException){
    (new ErrorResponse())->send();
    return;
}


$routes = [
    'GET' =>[
        '/users/show' => new FindByUserName(new SqliteUsersRepository(new PDO ('sqlite:'. __DIR__ .'/blog.sqlite'))),
    ],
    'POST' => [
        '/posts/create' => new CreatePosts(
            new SqlitePostsRepository(new PDO ('sqlite:'. __DIR__ .'/blog.sqlite'),new SqliteUsersRepository(new PDO ('sqlite:'. __DIR__ .'/blog.sqlite'))),
            new SqliteUsersRepository(new PDO ('sqlite:'. __DIR__ .'/blog.sqlite'))
        )
        //    '/posts/show' => new FindByUuid(new SqliteUsersRepository(new PDO ('sqlite:'. __DIR__ .'/blog.sqlite'))),
    ]


];


if(!array_key_exists($path,$routes[$method])){
    (new ErrorResponse('Not found'))->send();
    return;
}

if(!array_key_exists($method,$routes)){
    (new ErrorResponse('Not found'))->send();
    return;
}

$action = $routes[$method][$path];

try {
    $response = $action->handle($request);
}catch (AppException $e){
    (new ErrorResponse($e->getMessage()))->send();
    return;
}

$response->send();