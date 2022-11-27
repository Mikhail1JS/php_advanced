<?php

namespace Project\Api\Http\Actions\Posts;

use Project\Api\Blog\Exceptions\HttpException;
use Project\Api\Blog\Exceptions\InvalidArgumentException;
use Project\Api\Blog\Exceptions\UserNotFoundException;
use Project\Api\Blog\Post;
use Project\Api\Blog\Repositories\PostsRepositories\PostsRepositoryInterface;
use Project\Api\Blog\Repositories\UsersRepositories\UsersRepositoryInterface;
use Project\Api\Blog\UUID;
use Project\Api\Http\Actions\ActionInterface;
use Project\Api\Http\ErrorResponse;
use Project\Api\Http\Request;
use Project\Api\Http\Response;
use Project\Api\Http\SuccessfulResponse;

class CreatePosts implements ActionInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        private UsersRepositoryInterface $usersRepository
    )
    {
    }

    public function handle(Request $request): Response
    {

      try{
          $authorUuid = new UUID($request->jsonBodyField('author_uuid')) ;
      }catch (HttpException | InvalidArgumentException $e) {
          return new ErrorResponse($e->getMessage());
      }

      try {
         $user = $this->usersRepository->get($authorUuid);
      } catch (UserNotFoundException $e) {
          return new ErrorResponse($e->getMessage());
      }

      $newPostUuid = UUID::random();

      try {
          $post = new Post (
              $newPostUuid,
              $user,
              $request->jsonBodyField('title'),
              $request->jsonBodyField('text')
          );
      }catch(HttpException $e){
          return new ErrorResponse($e->getMessage());
      }


      $this->postsRepository->save($post);

      return new SuccessfulResponse([
          'uuid'=> (string) $newPostUuid
      ]);

    }
}