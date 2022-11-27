<?php

namespace Project\Api\Http\Actions\Posts;

use Project\Api\Blog\Exceptions\HttpException;
use Project\Api\Blog\Exceptions\InvalidArgumentException;
use Project\Api\Blog\Exceptions\PostNotFoundException;
use Project\Api\Blog\Repositories\PostsRepositories\PostsRepositoryInterface;
use Project\Api\Blog\UUID;
use Project\Api\Http\Actions\ActionInterface;
use Project\Api\Http\ErrorResponse;
use Project\Api\Http\Request;
use Project\Api\Http\Response;
use Project\Api\Http\SuccessfulResponse;

class DeletePost implements ActionInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository
    ){

    }

    public function handle(Request $request): Response
    {
       try {
           $postUuid = new UUID($request->query('uuid'));
       } catch (HttpException |InvalidArgumentException $e) {
           return new ErrorResponse($e->getMessage());
       }

       try {
           $this->postsRepository->delete($postUuid);
       } catch (PostNotFoundException $e) {
           return new ErrorResponse($e->getMessage());
       }

       return new SuccessfulResponse([
           'uuid'=> (string) $postUuid
       ]);
    }
}