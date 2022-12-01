<?php

namespace Project\Api\Blog\Repositories\PostsLikesRepositories;

use PDO;
use Project\Api\Blog\Exceptions\LikeAlreadyExistsException;
use Project\Api\Blog\Exceptions\LikeNotFoundException;
use Project\Api\Blog\Likes\PostLike;
use Project\Api\Blog\UUID;

class SqlitePostsLikesRepository implements PostsLikesRepositoryInterface
{

    public function __construct( private PDO $connection )
    {}

    public function save(PostLike $like): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO posts_likes (uuid,user_uuid,post_uuid) VALUES (:uuid, :user_uuid, :post_uuid)'
        );

        $statement->execute(
            [
                ':uuid'=>$like->uuid(),
                ':user_uuid'=>$like->getUserUuid(),
                ':post_uuid'=>$like->getPostUuid()
            ]
        );

    }

    /**
     * @throws LikeNotFoundException
     */
    public function getByPostUuid(UUID $postUuid): array
    {

        $statement = $this->connection->prepare(
            'SELECT * FROM posts_likes WHERE post_uuid = :uuid'
        );

        $statement->execute([
            ':uuid'=> $postUuid
        ]);

        $result = $statement->fetchAll();

        if(!$result) {
            throw new LikeNotFoundException("Not found likes for the post $postUuid");
        }

        return $result;
    }

    /**
     * @throws LikeAlreadyExistsException
     */
    public function isLikeAlreadyExist (PostLike $like): void {

        $statement = $this->connection->prepare(
            'SELECT * FROM posts_likes WHERE post_uuid = :post_uuid AND user_uuid = :user_uuid'
        );

        $statement->execute([
            ':post_uuid'=> $like->getPostUuid(),
            ':user_uuid'=> $like->getUserUuid()
        ]);

        $result = $statement->fetch();


        if($result){
            throw new LikeAlreadyExistsException('Like already exists');
        }

     }
}