<?php

namespace Project\Api\Http\Actions\Posts;

use Project\Api\Blog\Exceptions\AuthException;
use Project\Api\Blog\Exceptions\HttpException;
use Project\Api\Blog\Post;
use Project\Api\Blog\Repositories\PostsRepositories\PostsRepositoryInterface;
use Project\Api\Blog\UUID;
use Project\Api\Http\Actions\ActionInterface;
use Project\Api\Http\Auth\IdentificationInterface;
use Project\Api\Http\ErrorResponse;
use Project\Api\Http\Request;
use Project\Api\Http\Response;
use Project\Api\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class CreatePosts implements ActionInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        private IdentificationInterface  $identification,
        private LoggerInterface          $logger
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $author = $this->identification->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $newPostUuid = UUID::random();

        try {
            $post = new Post (
                $newPostUuid,
                $author,
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text')
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }


        $this->postsRepository->save($post);

        $this->logger->info("Post created: $newPostUuid");

        return new SuccessfulResponse([
            'uuid' => (string)$newPostUuid
        ]);

    }
}