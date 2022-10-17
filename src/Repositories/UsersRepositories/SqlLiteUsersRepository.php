<?php

namespace Project\Api\Repositories\UsersRepositories;

use PDO;
use Project\Api\Blog\User;

class SqlLiteUsersRepository
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
}