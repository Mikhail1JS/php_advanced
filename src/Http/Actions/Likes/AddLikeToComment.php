<?php

namespace Project\Api\Http\Actions\Likes;

use Project\Api\Blog\Exceptions\HttpException;
use Project\Api\Blog\Exceptions\InvalidArgumentException;
use Project\Api\Blog\Exceptions\LikeAlreadyExistsException;
use Project\Api\Blog\Exceptions\PostNotFoundException;
use Project\Api\Blog\Exceptions\UserNotFoundException;
use Project\Api\Blog\Likes\CommentLike;
use Project\Api\Blog\Repositories\CommentsLikesRepositories\CommentsLikesRepositoriesInterface;
use Project\Api\Blog\Repositories\CommentsRepositories\CommentsRepositoriesInterface;
use Project\Api\Blog\Repositories\UsersRepositories\UsersRepositoryInterface;
use Project\Api\Blog\UUID;
use Project\Api\Http\Actions\ActionInterface;
use Project\Api\Http\ErrorResponse;
use Project\Api\Http\Request;
use Project\Api\Http\Response;
use Project\Api\Http\SuccessfulResponse;

class AddLikeToComment implements ActionInterface
{
    public function __construct(
        private CommentsLikesRepositoriesInterface $commentsLikesRepository,
        private CommentsRepositoriesInterface $commentsRepository,
        private UsersRepositoryInterface $usersRepository
    )
    {}

    public function handle(Request $request): Response
    {
        try {
            $userUuid = $request->jsonBodyField('user_uuid');
            $commentUuid = $request->jsonBodyField('comment_uuid');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $user = $this->usersRepository->get(new UUID($userUuid));
            $comment = $this->commentsRepository->get(new UUID($commentUuid));
        } catch (UserNotFoundException|PostNotFoundException|InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $commentLike = new CommentLike(UUID::random(),$comment,$user);

        try{
            $this->commentsLikesRepository->isLikeAlreadyExist($commentLike);
        } catch (LikeAlreadyExistsException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->commentsLikesRepository->save($commentLike);

        return new SuccessfulResponse([
            'uuid'=>(string)$commentLike
        ]);

    }
}