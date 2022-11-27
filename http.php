<?php
use Project\Api\Blog\Exceptions\AppException;
use Project\Api\Blog\Exceptions\HttpException;
use Project\Api\Http\Actions\Comments\CreateComment;
use Project\Api\Http\Actions\Posts\CreatePosts;
use Project\Api\Http\Actions\Posts\DeletePost;
use Project\Api\Http\Actions\Users\CreateUser;
use Project\Api\Http\Actions\Users\FindByUserName;
use Project\Api\Http\ErrorResponse;
use Project\Api\Http\Request;

$container = require __DIR__.'/bootstrap.php';

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input'));

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
        '/users/show' => FindByUserName::class
    ],
    'DELETE' => [
        '/posts' => DeletePost::class
    ],
    'POST' => [
        '/posts/create' => CreatePosts::class,
        '/users/create' => CreateUser::class,
        '/comments/create' => CreateComment::class
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

$actionClassName = $routes[$method][$path];
$action = $container->get($actionClassName);

try {
    $response = $action->handle($request);
}catch (AppException $e){
    (new ErrorResponse($e->getMessage()))->send();
    return;
}

$response->send();