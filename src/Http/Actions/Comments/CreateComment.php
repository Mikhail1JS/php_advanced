<?php

namespace Project\Api\Http\Actions\Comments;

use Project\Api\Blog\Comment;
use Project\Api\Blog\Exceptions\AlreadyRegisteredException;
use Project\Api\Blog\Exceptions\HttpException;
use Project\Api\Blog\Exceptions\InvalidArgumentException;
use Project\Api\Blog\Exceptions\PostNotFoundException;
use Project\Api\Blog\Exceptions\UserNotFoundException;
use Project\Api\Blog\Repositories\CommentsRepositories\CommentsRepositoriesInterface;
use Project\Api\Blog\Repositories\CommentsRepositories\SqliteCommentsRepository;
use Project\Api\Blog\Repositories\PostsRepositories\PostsRepositoryInterface;
use Project\Api\Blog\Repositories\PostsRepositories\SqlitePostsRepository;
use Project\Api\Blog\Repositories\UsersRepositories\SqliteUsersRepository;
use Project\Api\Blog\Repositories\UsersRepositories\UsersRepositoryInterface;
use Project\Api\Blog\UUID;
use Project\Api\Http\Actions\ActionInterface;
use Project\Api\Http\ErrorResponse;
use Project\Api\Http\Request;
use Project\Api\Http\Response;
use Project\Api\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class CreateComment implements ActionInterface
{

    public function __construct(
        private UsersRepositoryInterface      $userRepository,
        private PostsRepositoryInterface      $postsRepository,
        private CommentsRepositoriesInterface $commentsRepository,
        private LoggerInterface               $logger)
    {

    }

    public function handle(Request $request): Response
    {
        try {
            $authorUuid = new UUID($request->jsonBodyField('author_uuid'));
            $postUuid = new UUID($request->jsonBodyField('post_uuid'));
            $text = $request->jsonBodyField('text');
        } catch (HttpException|InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $user = $this->userRepository->get($authorUuid);
            $post = $this->postsRepository->get($postUuid);
        } catch (UserNotFoundException|PostNotFoundException|InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $newCommentUuid = UUID::random();
        $newComment = new Comment($newCommentUuid, $user, $post, $text);


        $this->commentsRepository->save($newComment);

        $this->logger->info("Comment created: $newCommentUuid");


        return new SuccessfulResponse([
            'uuid' => (string)$newCommentUuid
        ]);
    }
}