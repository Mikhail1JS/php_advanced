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
use Psr\Log\LoggerInterface;

class CreateUserCommandTest extends TestCase
{


    public function makeUserRepository($value = 'empty'): UsersRepositoryInterface
    {

        if ($value === 'user') {
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

        return new class implements UsersRepositoryInterface {

            public function save(User $user): void
            {

            }

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("Not found");

            }
        };


    }

    /**
     * @throws ArgumentsException
     */
    public function testItThrowsAnExceptionWhenUserAlreadyExists(): void
    {

        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();

        $command = new CreateUserCommand($this->makeUserRepository('user'), $logger);

        $this->expectException(CommandException::class);

        $this->expectExceptionMessage("User already exists: user");

        $command->handle(new Arguments(['username' => 'user', 'password' => 'password']));

    }


    public function testItRequiresFirstName(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();

        $command = new CreateUserCommand($this->makeUserRepository(), $logger);

        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage("No such argument: first_name");

        $command->handle(new Arguments(['username' => 'user', 'password' => 'password']));

    }

    public function testItRequiresLastName(): void
    {

        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();

        $command = new CreateUserCommand($this->makeUserRepository(), $logger);

        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage("No such argument: last_name");

        $command->handle(new Arguments(['username' => 'user', 'password' => 'password', 'first_name' => 'Black']));

    }

    public function testItSavesUserToRepository(): void
    {

        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();

        $userRepo = $this->createMock(UsersRepositoryInterface::class);

        $userRepo->method('getByUsername')->will($this->throwException(new UserNotFoundException));

        $command = new CreateUserCommand($userRepo, $logger);

        $userRepo->expects($this->once())
            ->method('save');

        $command->handle(new Arguments(['username' => 'log', 'password' => 'password', 'first_name' => 'Alex', 'last_name' => 'White']));

    }

    public function testItRequiresPassword(): void
    {

        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();

        $command = new CreateUserCommand(
            $this->makeUserRepository(),
            $logger,
        );

        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage("No such argument: password");

        $command->handle(new Arguments(['username' => 'Fire92']));
    }

}