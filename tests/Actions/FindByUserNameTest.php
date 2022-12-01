<?php

namespace Actions;

use PHPUnit\Framework\TestCase;
use Project\Api\Blog\Exceptions\UserNotFoundException;
use Project\Api\Blog\Repositories\UsersRepositories\UsersRepositoryInterface;
use Project\Api\Blog\User;
use Project\Api\Blog\UUID;
use Project\Api\Http\Actions\Users\FindByUserName;
use Project\Api\Http\ErrorResponse;
use Project\Api\Http\Request;
use Project\Api\Http\SuccessfulResponse;
use Project\Api\Person\Name;
use Psr\Log\LoggerInterface;

class FindByUserNameTest extends TestCase
{
    /**
     *@runInSeparateProcess
     *@preserveGlobalState disabled
     */

    public function testItReturnsErrorResponseIfNoUserNameProvided ():void {

        $request = new Request([],[],'test');
        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $userRepository = $this->userRepository([]);
        $action = new FindByUserName($userRepository,$logger);
        $response = $action->handle($request);

        $response->send();

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"No such query param in the request: username"}');

    }

    /**
     *@runInSeparateProcess
     *@preserveGlobalState disabled
     */

    public function testItReturnsErrorResponseIfUserNotFound ():void {

        $request = new Request(['username' => 'Fire92'],[], 'test');
        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $userRepository = $this->userRepository([]);
        $action = new FindByUserName($userRepository,$logger);
        $response = $action->handle($request);

        $response->send();

        $this->assertInstanceOf(ErrorResponse::class, $response);

        $this->expectOutputString('{"success":false,"reason":"User not found : Fire92"}');
    }

    /**
     *@runInSeparateProcess
     *@preserveGlobalState disabled
     */

    public function testItReturnsSuccessfulResponse (): void {

        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $request = new Request(['username'=> 'Fire92'], [], 'test');

        $userRepository = $this->userRepository(
            [
                new User(UUID::random(),
                    'Fire92',
                    new Name('Tom','White')),
            ]
        );

        $action = new FindByUserName($userRepository,$logger);

        $response = $action->handle($request);

        $response->send();

        $this->assertInstanceOf(SuccessfulResponse::class,$response);

        $this->expectOutputString('{"success":true,"data":{"username":"Fire92","name":"Tom White"}}');

    }


    private function userRepository (array $users): UsersRepositoryInterface{
        return new class ($users) implements UsersRepositoryInterface {

            public function __construct(private array $users){

            }

            public function save(User $user): void
            {
                // TODO: Implement save() method.
            }

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException('User not found');
            }

            public function getByUsername(string $username): User
            {
                foreach ($this->users as $user) {
                    if ($user instanceof User && $user->username() === $username) {
                        return $user;
                    }
                }
                throw new UserNotFoundException("User not found : $username");
            }
        };
    }
}