<?php

namespace Commands;

use PHPUnit\Framework\TestCase;
use Project\Api\Blog\Commands\Arguments;
use Project\Api\Blog\Commands\CreateUser;
use Project\Api\Blog\Commands\CreateUserCommand;
use Project\Api\Blog\Exceptions\ArgumentsException;
use Project\Api\Blog\Exceptions\CommandException;
use Project\Api\Blog\Exceptions\UserNotFoundException;
use Project\Api\Blog\Repositories\UsersRepositories\UsersRepositoryInterface;
use Project\Api\Blog\User;
use Project\Api\Blog\UUID;
use Project\Api\Person\Name;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class CreateUserCommandTest extends TestCase
{

    public function testItRequiresFirstName(): void
    {
        $command = new CreateUser(
            $this->makeUserRepository()
        );

        $this->expectException(RuntimeException::class);

        $this->expectExceptionMessage(
            'Not enough arguments (missing: "first_name").'
        );

        $command->run(
            new ArrayInput([
                'username'=> 'Ivan',
                'password'=> 'some_password',
                'last_name'=>'Ivan'
            ]),
            new NullOutput()
        );

    }

    public function testItRequiresLastName(): void
    {

        $command = new CreateUser(
            $this->makeUserRepository()
        );

        $this->expectException(RuntimeException::class);

        $this->expectExceptionMessage(
            'Not enough arguments (missing: "last_name").'
        );

        $command->run(
            new ArrayInput([
                'username'=> 'Ivan',
                'password'=> 'some_password',
                'first_name'=>'Ivan'
            ]),
            new NullOutput()
        );

    }

    public function testItRequiresPassword(): void
    {

        $command = new CreateUser(
            $this->makeUserRepository()
        );

        $this->expectException(RuntimeException::class);

        $this->expectExceptionMessage(
            'Not enough arguments (missing: "password").'
        );

        $command->run(
            new ArrayInput([
                'username'=> 'Ivan',
                'last_name'=> 'some_name',
                'first_name'=>'Ivan'
            ]),
            new NullOutput()
        );
    }

    public function testItSavesUserToRepository(): void
    {
        $userRepository = new class implements UsersRepositoryInterface {

            private bool $called = false;

            public function save(User $user): void
            {
                $this->called = true;
            }

            public function get(UUID $uuid): User
            {
                return new User(UUID::random(), 'user', 'password', new Name("first", 'last'));

            }

            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException('');

            }

            public function wasCalled (): bool {
                return $this->called;
            }
        };

        $command = new CreateUser(
           $userRepository
        );

        $command->run(
            new ArrayInput([
                'username'=> 'Ivan',
                'last_name'=> 'some_name',
                'first_name'=>'Ivan',
                'password' => 'some_password'
            ]),
            new NullOutput()
        );


        $this->assertTrue($userRepository->wasCalled());
    }



    public function makeUserRepository(): UsersRepositoryInterface
    {
            return new class implements UsersRepositoryInterface {


                public function save(User $user): void
                {

                }

                public function get(UUID $uuid): User
                {
                    return new User(UUID::random(), 'user', 'password', new Name("first", 'last'));

                }

                public function getByUsername(string $username): User
                {
                    return new User(UUID::random(), 'user', 'password', new Name("first", 'last'));

                }
            };
        }

}