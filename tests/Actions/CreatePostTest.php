<?php

namespace Actions;

use PHPUnit\Framework\TestCase;
use Project\Api\Blog\Exceptions\HttpException;
use Project\Api\Blog\Exceptions\UserNotFoundException;
use Project\Api\Blog\Repositories\UsersRepositories\UsersRepositoryInterface;
use Project\Api\Blog\Repositories\PostsRepositories\PostsRepositoryInterface;
use Project\Api\Blog\User;
use Project\Api\Blog\UUID;
use Project\Api\Http\Actions\Posts\CreatePosts;
use Project\Api\Http\Auth\JsonBodyUuidIdentification;
use Project\Api\Http\ErrorResponse;
use Project\Api\Http\Request;
use Project\Api\Http\SuccessfulResponse;
use Project\Api\Person\Name;
use Psr\Log\LoggerInterface;

class CreatePostTest extends TestCase
{
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */

    public function testItReturnsSuccessfulResponse(): void
    {
        $data = json_encode([
            'user_uuid' => '69265fe0-6ba4-43b4-85bc-bcedeb31e6ba',
            'title' => 'someTitle',
            'text' => 'someText',
        ]);

        $request = new Request([], [], $data);
        $usersRepository = $this->userRepository();
        $auth = new JsonBodyUuidIdentification($usersRepository);
        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $postRepository = $this->createMock(PostsRepositoryInterface::class);
        $action = new CreatePosts($postRepository, $auth, $logger);
        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);

    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */

    public function testItReturnsAnErrorWhenWrongUuid(): void
    {

        $request = $this->getMockBuilder(Request::class)
            ->onlyMethods(['jsonBodyField'])
            ->setConstructorArgs(array([], [], 'test'))
            ->getMock();

        $request->expects($this->any())->method('jsonBodyField')->willReturn('testValue');


        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $usersRepository = $this->createMock(UsersRepositoryInterface::class);
        $auth = new JsonBodyUuidIdentification($usersRepository);
        $postsRepository = $this->createMock(PostsRepositoryInterface::class);

        $action = new CreatePosts($postsRepository, $auth, $logger);

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);

        $response->send();

        $this->expectOutputString('{"success":false,"reason":"Malformed UUID: testValue"}');


    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */

    public function testItReturnsAnErrorWhenUserNotFoundByUuid(): void
    {

        $request = $this->getMockBuilder(Request::class)
            ->onlyMethods(['jsonBodyField'])
            ->setConstructorArgs(array([], [], 'test'))
            ->getMock();

        $request->expects($this->any())->method('jsonBodyField')->willReturn('69265fe0-6ba4-43b4-85bc-bcedeb31e6ba');

        $usersRepository = $this->createMock(UsersRepositoryInterface::class);

        $usersRepository
            ->method('get')
            ->willThrowException(new UserNotFoundException("Cannot get user: 69265fe0-6ba4-43b4-85bc-bcedeb31e6ba"));

        $auth = new JsonBodyUuidIdentification($usersRepository);

        $postsRepository = $this->createMock(PostsRepositoryInterface::class);
        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();

        $action = new CreatePosts($postsRepository, $auth, $logger);

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);

        $response->send();

        $this->expectOutputString('{"success":false,"reason":"Cannot get user: 69265fe0-6ba4-43b4-85bc-bcedeb31e6ba"}');


    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */

    public function testItReturnsAnErrorWhenRequestHasNotRequireDataForPost(): void
    {

        $request = $this->requestStub([], [], 'test');
        $usersRepository = $this->userRepository();
        $postsRepository = $this->createMock(PostsRepositoryInterface::class);
        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $auth = new JsonBodyUuidIdentification($usersRepository);
        $action = new CreatePosts($postsRepository, $auth, $logger);
        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);

        $response->send();

        $this->expectOutputString('{"success":false,"reason":"No such field:title"}');


    }

    private function requestStub(array $get, array $server, string $stringValue,): Request
    {
        return new class ($get, $server, $stringValue) extends Request {

            public function jsonBodyField(string $field): mixed
            {
                $data = [
                    'user_uuid' => '69265fe0-6ba4-43b4-85bc-bcedeb31e6ba',
                    'text' => 'someText',
                ];

                if (!array_key_exists($field, $data)) {
                    throw new HttpException("No such field:$field");
                }

                return $data[$field];
            }

        };


    }

    private function userRepository(): UsersRepositoryInterface
    {
        return new class implements UsersRepositoryInterface {

            public function save(User $user): void
            {
                // TODO: Implement save() method.
            }

            public function get(UUID $uuid): User
            {
                return new User (
                    new UUID('69265fe0-6ba4-43b4-85bc-bcedeb31e6ba'),
                    'Fire92',
                    'password',
                    new Name("John", 'Black')
                );
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