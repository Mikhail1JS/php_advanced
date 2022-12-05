<?php

namespace Project\Api\Blog\Repositories\UsersRepositories;

use PDO;
use PDOStatement;
use Project\Api\Blog\Exceptions\AlreadyRegisteredException;
use Project\Api\Blog\Exceptions\InvalidArgumentException;
use Project\Api\Blog\Exceptions\UserNotFoundException;
use Project\Api\Blog\User;
use Project\Api\Blog\UUID;
use Project\Api\Person\Name;

class SqliteUsersRepository implements UsersRepositoryInterface
{
    public function __construct(
        private PDO $connection
    )
    {}

    /**
     * @throws AlreadyRegisteredException
     */
    public function save(User $user):void{

        $statement = $this->connection->prepare(
            'INSERT INTO users (uuid , username , password, first_name , last_name) VALUES (:uuid , :username , :password, :first_name , :last_name)'
        );

        $result = $statement->execute([
            ':uuid' => $user->uuid(),
            ':username' => $user->username(),
            ':password' => $user->hashedPassword(),
            ':first_name' => $user->getFirstName(),
            ':last_name' => $user->getLastName()
        ]);

        if (!$result) {
            throw new AlreadyRegisteredException('Username or UUID already registered ');
        }
    }


    /**
     * @param UUID $uuid
     * @return User
     * @throws InvalidArgumentException
     * @throws UserNotFoundException
     */
    public function get(UUID $uuid): User{
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE uuid = :uuid '
        );

        $statement->execute([
            ':uuid'=> $uuid
        ]);
        return $this->getUser($statement,$uuid);
    }

    /**
     * @throws UserNotFoundException|InvalidArgumentException
     */
    public function getByUsername(string $username): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE username = :username');

        $statement->execute([
            ':username'=>$username
        ]);

        return $this->getUser($statement,$username);
    }

    /**
     * @throws UserNotFoundException|InvalidArgumentException
     */
    private function getUser(PDOStatement $statement, $value): User
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if($result === false){
            throw new UserNotFoundException(
                "Cannot get user: $value"
            );
        }

        return new User (
            new UUID ($result['uuid']),
            $result['username'],
            $result['password'],
            new Name ($result['first_name'], $result['last_name'])
        );
    }
}