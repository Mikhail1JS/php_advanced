<?php

namespace Project\Api\Blog\Repositories\PostsRepositories;

use PDO;
use PDOStatement;
use Project\Api\Blog\Exceptions\CommentNotFoundException;
use Project\Api\Blog\Exceptions\InvalidArgumentException;
use Project\Api\Blog\Exceptions\PostNotFoundException;
use Project\Api\Blog\Exceptions\UserNotFoundException;
use Project\Api\Blog\Post;
use Project\Api\Blog\Repositories\UsersRepositories\SqliteUsersRepository;
use Project\Api\Blog\User;
use Project\Api\Blog\UUID;


class SqlitePostsRepository implements PostsRepositoryInterface
{
    public function __construct(
        private PDO $connection,
        private SqliteUsersRepository $usersRepository
    )
    {}

    public function save(Post $post): void{

        $statement = $this->connection->prepare(
            'INSERT INTO posts (uuid , author_uuid , title , text) VALUES (:uuid , :author_uuid , :title , :text)'
        );

        $statement->execute([
            ':uuid' => $post->uuid(),
            ':author_uuid' => $post->getAuthorUuid(),
            ':title' => $post->getTitle(),
            ':text' => $post->getText()
        ]);
    }

    /**
     * @throws PostNotFoundException
     */
    public function delete(UUID $uuid): void {

        $statement = $this->connection->prepare(
            'DELETE from posts WHERE uuid = :uuid'
        );

       $statement->execute([
           ':uuid' => $uuid
        ]);

        if($statement->rowCount() < 1) {
            throw new PostNotFoundException('No post with such uuid');
        }
    }


    /**
     * @throws InvalidArgumentException
     * @throws UserNotFoundException
     * @throws PostNotFoundException
     */
    public function get(UUID $uuid): Post{
        $statement = $this->connection->prepare(
            'SELECT * FROM posts WHERE uuid = :uuid '
        );

        $statement->execute([
            ':uuid'=> $uuid
        ]);
        return $this->getPost($statement,$uuid);
    }

    /**
     * @throws InvalidArgumentException|PostNotFoundException|UserNotFoundException
     */
    public function getByTitle(string $title): Post
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM posts WHERE title = :title');

        $statement->execute([
            ':title'=>$title
        ]);

        return $this->getPost($statement,$title);
    }

    /**
     * @throws InvalidArgumentException
     * @throws UserNotFoundException
     * @throws PostNotFoundException
     */
    private function getPost(PDOStatement $statement, $value): Post
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if($result === false){
            throw new PostNotFoundException(
                "Cannot get post: $value"
            );
        }

        $user = $this->usersRepository->get(new UUID($result['author_uuid']));

        return new Post (
            new UUID ($result['uuid']),
            $user,
            $result['title'],
            $result['text']
        );
    }
}