<?php
use Project\Api\Blog\Exceptions\AppException;
use Project\Api\Blog\Exceptions\HttpException;
use Project\Api\Http\Actions\Comments\CreateComment;
use Project\Api\Http\Actions\Likes\AddLikeToComment;
use Project\Api\Http\Actions\Likes\AddLikeToPost;
use Project\Api\Http\Actions\Posts\CreatePosts;
use Project\Api\Http\Actions\Posts\DeletePost;
use Project\Api\Http\Actions\Users\CreateUser;
use Project\Api\Http\Actions\Users\FindByUserName;
use Project\Api\Http\Auth\LogIn;
use Project\Api\Http\ErrorResponse;
use Project\Api\Http\Request;
use Psr\Log\LoggerInterface;

$container = require __DIR__.'/bootstrap.php';

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input'));

$logger = $container->get(LoggerInterface::class);

try {
   $path = $request->path();
}catch (HttpException $e){
    $logger->warning($e->getMessage())
    (new ErrorResponse)->send();
    return;
}

try {
    $method = $request->method();
}catch(HttpException $e){
    $logger->warning($e->getMessage())
    (new ErrorResponse)->send();
    return;
}

$routes = [
    'GET' =>[
        '/users/show' => FindByUserName::class
    ],
    'DELETE' => [
        '/posts' => DeletePost::class
    ],
    'POST' => [
        '/login' => LogIn::class,
        '/posts/create' => CreatePosts::class,
        '/posts/like' => AddLikeToPost::class,
        '/users/create' => CreateUser::class,
        '/comments/create' => CreateComment::class,
        '/comments/like' => AddLikeToComment::class
    ]
];


if(!array_key_exists($method,$routes) || !array_key_exists($path,$routes[$method])){
    $message = "Route not found: $method $path";
    $logger->notice($message);
    (new ErrorResponse('Not found'))->send();
    return;
}


$actionClassName = $routes[$method][$path];

try {
    $action = $container->get($actionClassName);
    $response = $action->handle($request);
}catch (AppException $e){
    $logger->error($e->getMessage(), ['exception' => $e])
    (new ErrorResponse($e->getMessage()))->send();
    return;
}

$response->send();