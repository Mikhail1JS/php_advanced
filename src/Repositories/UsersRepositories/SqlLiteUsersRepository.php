<?php

namespace Project\Api\Repositories\UsersRepositories;

use PDO;
use Project\Api\Blog\User;
use Project\Api\Blog\UUID;
use Project\Api\Exceptions\UserNotFoundException;
use Project\Api\Person\Name;

class SqlLiteUsersRepository implements UsersRepositoryInterface
{
    public function __construct(
        private PDO $connection
    )
    {}

    public function save(User $user):void{

        $statement = $this->connection->prepare(
            'INSERT INTO users (uuid , username , first_name , last_name) VALUES (:uuid , :username , :first_name , :last_name)'
        );

        $statement->execute([
            ':uuid' => $user->uuid(),
            ':username' => $user->getUserName(),
            ':first_name' => $user->getFirstName(),
            ':last_name' => $user->getLastName()
        ]);
    }

    /**
     * @throws UserNotFoundException
     */
    public function get(UUID $uuid){
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE uuid = :uuid '
        );

        $statement->execute([
            ':uuid'=> $uuid
        ]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if($result === false){
            throw new UserNotFoundException(
                "Cannot get user: $uuid"
            );
        }

        return new User (
            new UUID ($result['uuid']),
            $result['username'],
            new Name ($result['first_name'], $result['last_name'])
        );
    }
}