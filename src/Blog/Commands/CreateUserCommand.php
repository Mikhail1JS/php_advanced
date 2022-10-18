<?php

namespace Project\Api\Blog\Commands;

use Project\Api\Blog\Exceptions\ArgumentsException;
use Project\Api\Blog\Exceptions\CommandException;
use Project\Api\Blog\Exceptions\UserNotFoundException;
use Project\Api\Blog\Repositories\UsersRepositories\UsersRepositoryInterface;
use Project\Api\Blog\User;
use Project\Api\Blog\UUID;
use Project\Api\Person\Name;

class CreateUserCommand
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    )
    {

    }

    /**
     * @throws CommandException
     * @throws ArgumentsException
     */
    public function handle(Arguments $arguments): void
    {
        $username = $arguments->get('username');

        if ($this->userExists($username)) {
            throw new CommandException("User already exists: $username");
        }

        $this->usersRepository->save(new User(
            UUID::random(),
            $username,
            new Name($arguments['first_name'], $arguments['last_name'])));
    }


    private function userExists(string $username): bool {
        try {
            $this->usersRepository->getByUsername($username);
        }catch (UserNotFoundException){
            return false;
        }
        return true;
    }
}