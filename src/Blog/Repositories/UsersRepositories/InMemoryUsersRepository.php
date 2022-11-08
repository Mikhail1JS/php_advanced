<?php

namespace Project\Api\Blog\Repositories\UsersRepositories;

use Project\Api\Blog\UUID;
use Project\Api\Blog\Exceptions\UserNotFoundException;
use Project\Api\Blog\User;

class InMemoryUsersRepository implements UsersRepositoryInterface
{
    public array $users = [];

    public function save(User $user): void
    {
        $this->users[] = $user;
    }

    /**
     * @throws UserNotFoundException
     */
    public function get(UUID $uuid): User
    {
        foreach ($this->users as $user) {
            if ((string)$user->uuid() === (string)$uuid) {
                return $user;
            }
        }
        throw new UserNotFoundException("User not found: $uuid");
    }


    /**
     * @throws UserNotFoundException
     */
    public function getByUsername(string $username ): User
    {
        foreach ($this->users as $user) {
            if ($user->username() === $username) {
                return $user;
            }
        }
        throw new UserNotFoundException("User not found: $username");
    }

}

