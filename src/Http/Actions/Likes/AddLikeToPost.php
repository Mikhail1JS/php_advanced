<?php

namespace Project\Api\Http\Actions\Likes;

use Project\Api\Blog\Exceptions\AuthException;
use Project\Api\Blog\Exceptions\HttpException;
use Project\Api\Blog\Exceptions\InvalidArgumentException;
use Project\Api\Blog\Exceptions\LikeAlreadyExistsException;
use Project\Api\Blog\Exceptions\PostNotFoundException;
use Project\Api\Blog\Exceptions\UserNotFoundException;
use Project\Api\Blog\Likes\PostLike;
use Project\Api\Blog\Repositories\PostsLikesRepositories\PostsLikesRepositoryInterface;
use Project\Api\Blog\Repositories\PostsRepositories\PostsRepositoryInterface;
use Project\Api\Blog\Repositories\UsersRepositories\UsersRepositoryInterface;
use Project\Api\Blog\UUID;
use Project\Api\Http\Actions\ActionInterface;
use Project\Api\Http\Auth\TokenAuthenticationInterface;
use Project\Api\Http\ErrorResponse;
use Project\Api\Http\Request;
use Project\Api\Http\Response;
use Project\Api\Http\SuccessfulResponse;

class AddLikeToPost implements ActionInterface
{

    public function __construct(
        private PostsLikesRepositoryInterface $postLikesRepository,
        private PostsRepositoryInterface      $postsRepository,
        private TokenAuthenticationInterface  $authentication
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $user = $this->authentication->user($request);
        }catch (AuthException $e){
            return new ErrorResponse($e->getMessage());
        }

        try {
            $postUuid = $request->jsonBodyField('post_uuid');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $post = $this->postsRepository->get(new UUID($postUuid));
        } catch (UserNotFoundException|PostNotFoundException|InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $like = new PostLike(UUID::random(), $post, $user);

        try {
            $this->postLikesRepository->isLikeAlreadyExist($like);
        } catch (LikeAlreadyExistsException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->postLikesRepository->save($like);

        return new SuccessfulResponse([
            'uuid' => (string)$like
        ]);

    }

}
