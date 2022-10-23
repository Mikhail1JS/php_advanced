<?php

namespace Commands;

use PHPUnit\Framework\TestCase;
use Project\Api\Blog\Commands\Arguments;
use Project\Api\Blog\Commands\CreateUserCommand;
use Project\Api\Blog\Exceptions\ArgumentsException;
use Project\Api\Blog\Exceptions\CommandException;
use Project\Api\Blog\Exceptions\UserNotFoundException;
use Project\Api\Blog\Repositories\UsersRepositories\UsersRepositoryInterface;
use Project\Api\Blog\User;
use Project\Api\Blog\UUID;
use Project\Api\Person\Name;

class CreateUserCommandTest extends TestCase
{

    /**
     * @throws ArgumentsException
     */

    public function makeUserRepository($value = 'empty'): UsersRepositoryInterface {

        if ($value === 'user'){
            return new class implements UsersRepositoryInterface {

                public function save(User $user): void
                {

                }

                public function get(UUID $uuid): User
                {

                }

                public function getByUsername(string $username): User
                {
                    return new User(UUID::random(),'user', new Name("first",'last'));

                }
            };
        }

        return new class implements UsersRepositoryInterface {

            public function save(User $user): void
            {

            }

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Notfound");
            }

            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("Notfound");

            }
        };


    }

    /**
     * @throws ArgumentsException
     */
    public function testItThrowsAnExceptionWhenUserAlreadyExists(): void {


        $command = new CreateUserCommand($this->makeUserRepository('user'));

        $this->expectException(CommandException::class);

        $this->expectExceptionMessage("User already exists: user");

        $command->handle(new Arguments(['username' => 'user']));

    }


    public function testItRequiresFirstName(): void  {

        $command = new CreateUserCommand($this->makeUserRepository());

        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage("No such argument: first_name");

        $command->handle(new Arguments(['username' => 'user']));

}

    public function testItRequiresLastName(): void  {

        $command = new CreateUserCommand($this->makeUserRepository());

        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage("No such argument: last_name");

        $command->handle(new Arguments(['username' => 'user','first_name' => 'Anjela']));

    }

    public function testItSavesUserToRepository(): void {

        $userRepo = $this->createMock(UsersRepositoryInterface::class);
        $userRepo->method('getByUsername')->will($this->throwException(new UserNotFoundException));

        $command = new CreateUserCommand($userRepo);

        $userRepo->expects($this->once())
            ->method('save');

        $command->handle(new Arguments(['username'=>'log','first_name'=>'Alex','last_name'=>'White']));

    }

}