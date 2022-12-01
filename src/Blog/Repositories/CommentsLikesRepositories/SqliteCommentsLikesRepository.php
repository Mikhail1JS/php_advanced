<?php

namespace Project\Api\Blog\Repositories\CommentsLikesRepositories;

use PDO;
use Project\Api\Blog\Exceptions\LikeAlreadyExistsException;
use Project\Api\Blog\Exceptions\LikeNotFoundException;
use Project\Api\Blog\Likes\CommentLike;
use Project\Api\Blog\UUID;

class SqliteCommentsLikesRepository implements CommentsLikesRepositoriesInterface
{
    public function __construct(
        private PDO $connection){}

    public function save(CommentLike $commentLike): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO comments_likes (uuid,comment_uuid,user_uuid) VALUES (:uuid,:comment_uuid, :user_uuid)'
        );
        $statement->execute([
           ':uuid'=>$commentLike->uuid(),
           ':comment_uuid'=>$commentLike->getCommentUuid(),
           ':user_uuid'=>$commentLike->getUserUuid()
        ]);
    }

    /**
     * @throws LikeNotFoundException
     */
    public function getByCommentsUuid(UUID $commentUuid): array
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM comments_likes WHERE comment_uuid = :comment_uuid'
        );

        $statement->execute([
           'comment_uuid'=>$commentUuid
        ]);

        $result = $statement->fetchAll();

        if(!$result){
            throw new LikeNotFoundException("Not found likes for the comment $commentUuid");
        }

        return $result;

    }

    /**
     * @throws LikeAlreadyExistsException
     */
    public function isLikeAlreadyExist (CommentLike $commentLike): void {

        $statement = $this->connection->prepare(
            'SELECT * FROM comments_likes WHERE comment_uuid = :comment_uuid AND user_uuid = :user_uuid'
        );

        $statement->execute([
            ':comment_uuid'=> $commentLike->getCommentUuid(),
            ':user_uuid'=> $commentLike->getUserUuid()
        ]);

        $result = $statement->fetch();

        if($result){
            throw new LikeAlreadyExistsException('Like already exists');
        }

    }


}