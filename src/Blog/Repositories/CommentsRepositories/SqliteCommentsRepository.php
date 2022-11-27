<?php

namespace Project\Api\Blog\Repositories\CommentsRepositories;

use PDO;
use PDOStatement;
use Project\Api\Blog\Comment;
use Project\Api\Blog\Exceptions\AlreadyRegisteredException;
use Project\Api\Blog\Exceptions\CommentNotFoundException;
use Project\Api\Blog\Exceptions\InvalidArgumentException;
use Project\Api\Blog\Exceptions\PostNotFoundException;
use Project\Api\Blog\Exceptions\UserNotFoundException;
use Project\Api\Blog\Repositories\PostsRepositories\SqlitePostsRepository;
use Project\Api\Blog\Repositories\UsersRepositories\SqliteUsersRepository;
use Project\Api\Blog\UUID;

class SqliteCommentsRepository implements CommentsRepositoriesInterface
{
    public function __construct(
        private PDO $connection,
        private SqliteUsersRepository $usersRepository,
        private SqlitePostsRepository $postsRepository,

    )
    {}

    /**
     * @throws AlreadyRegisteredException
     */
    public function save(Comment $comment):void{

        $statement = $this->connection->prepare(
            'INSERT INTO comments (uuid , post_uuid , author_uuid , text) VALUES (:uuid , :post_uuid, :author_uuid , :text)'
        );

        $result = $statement->execute([
            ':uuid' => $comment->uuid(),
            ':post_uuid' => $comment->getPostUuid(),
            ':author_uuid' => $comment->getAuthorUuid(),
            ':text' => $comment->getText()
        ]);

        if(!$result){
            throw new AlreadyRegisteredException('Comment already registered');
        }
    }


    /**
     * @throws InvalidArgumentException
     * @throws UserNotFoundException
     * @throws PostNotFoundException|CommentNotFoundException
     */
    public function get(UUID $uuid): Comment{
        $statement = $this->connection->prepare(
            'SELECT * FROM comments WHERE uuid = :uuid '
        );

        $statement->execute([
            ':uuid'=> $uuid
        ]);
        return $this->getComment($statement,$uuid);
    }


    /**
     * @throws InvalidArgumentException
     * @throws UserNotFoundException
     * @throws PostNotFoundException
     * @throws CommentNotFoundException
     */
    private function getComment(PDOStatement $statement, $value): Comment
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if($result === false){
            throw new CommentNotFoundException(
                "Cannot find comment: $value"
            );
        }

        $user = $this->usersRepository->get(new UUID($result['author_uuid']));
        $post = $this->postsRepository->get(new UUID($result['post_uuid']));

        return new Comment (
            new UUID ($result['uuid']),
            $user,
            $post,
            $result['text']
        );
    }
}