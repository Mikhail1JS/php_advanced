<?php

namespace Repositories;

use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Project\Api\Blog\Exceptions\PostNotFoundException;
use Project\Api\Blog\Post;
use Project\Api\Blog\Repositories\CommentsRepositories\SqliteCommentsRepository;
use Project\Api\Blog\Repositories\PostsRepositories\SqlitePostsRepository;
use Project\Api\Blog\Repositories\UsersRepositories\SqliteUsersRepository;
use Project\Api\Blog\User;
use Project\Api\Blog\UUID;
use Project\Api\Person\Name;

class SqlPostsRepositoryTest extends TestCase
{

    public function testItThrowsAnExceptionWhenPostNotFound(): void
    {

        $connectionStub = $this->createMock(PDO::class);

        $statementStub = $this->createMock(PDOStatement::class);

        $statementStub->method('fetch')
            ->willReturn(false);

        $connectionStub->method('prepare')
            ->willReturn($statementStub);

        $userRepository = new SqliteUsersRepository($connectionStub);
        $postRepository = new SqlitePostsRepository($connectionStub, $userRepository);

        $this->expectException(PostNotFoundException::class);
        $this->expectExceptionMessage("Cannot get post: 69265fe0-6ba4-43b4-85bc-bcedeb31e6ba");

        $postRepository->get(new UUID("69265fe0-6ba4-43b4-85bc-bcedeb31e6ba"));

    }


    public function testItSavesPostToDataBase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid' => "5b9a7d5e-c221-4a91-805e-05b638f596ee",
                ':author_uuid' => "69265fe0-6ba4-43b4-85bc-bcedeb31e6ba",
                ':title' => "TestTitle",
                ':text' => "SomeText"]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $userRepository = new SqliteUsersRepository($connectionStub);
        $postRepository = new SqlitePostsRepository($connectionStub, $userRepository);

        $user = new User(new UUID("69265fe0-6ba4-43b4-85bc-bcedeb31e6ba"), "Fire92",'password', new Name('Tom', 'Black'));

        $post = new Post(new UUID("5b9a7d5e-c221-4a91-805e-05b638f596ee"), $user, 'TestTitle', 'SomeText');

        $postRepository->save($post);

    }


    public function testItGetPostFromDataBase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $userStatementMock = $this->createMock(PDOStatement::class);
        $postStatementMock = $this->createMock(PDOStatement::class);

        $userStatementMock->expects($this->once())
            ->method('execute')
            ->with([':uuid'=>'8b45aec5-d71c-42b1-9a62-c54229c46204']);

        $userStatementMock->expects($this->once())
            ->method('fetch')
            ->willReturn([
                'uuid' => '8b45aec5-d71c-42b1-9a62-c54229c46204',
                'username' => 'Fire92',
                'password' => 'password',
                'first_name' => 'John',
                'last_name'=> 'Black'
            ]);

        $postStatementMock->expects($this->once())
            ->method('execute')
            ->with([':uuid'=>'69265fe0-6ba4-43b4-85bc-bcedeb31e6ba']);

        $postStatementMock->expects($this->once())
            ->method('fetch')
            ->willReturn([
                'uuid' => '69265fe0-6ba4-43b4-85bc-bcedeb31e6ba',
                'title' => 'TestTitle',
                'text' => 'SomeText',
                'author_uuid' => '8b45aec5-d71c-42b1-9a62-c54229c46204',
                ]);

        $connectionStub->method('prepare')->willReturn($postStatementMock,$userStatementMock);

        $userRepository = new SqliteUsersRepository($connectionStub);

        $postRepository = new SqlitePostsRepository($connectionStub,$userRepository);

        $this->assertInstanceOf(Post::class,$postRepository->get(new UUID('69265fe0-6ba4-43b4-85bc-bcedeb31e6ba')));


    }


    public function testItGetPostByTitleFromDataBase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $userStatementMock = $this->createMock(PDOStatement::class);
        $postStatementMock = $this->createMock(PDOStatement::class);

        $userStatementMock->expects($this->once())
            ->method('execute')
            ->with([':uuid'=>'8b45aec5-d71c-42b1-9a62-c54229c46204']);

        $userStatementMock->expects($this->once())
            ->method('fetch')
            ->willReturn([
                'uuid' => '8b45aec5-d71c-42b1-9a62-c54229c46204',
                'username' => 'Fire92',
                'password' => 'password',
                'first_name' => 'John',
                'last_name'=> 'Black'
            ]);

        $postStatementMock->expects($this->once())
            ->method('execute')
            ->with([':title'=>'TestTitle']);

        $postStatementMock->expects($this->once())
            ->method('fetch')
            ->willReturn([
                'uuid' => '69265fe0-6ba4-43b4-85bc-bcedeb31e6ba',
                'title' => 'TestTitle',
                'text' => 'SomeText',
                'author_uuid' => '8b45aec5-d71c-42b1-9a62-c54229c46204',
            ]);

        $connectionStub->method('prepare')->willReturn($postStatementMock,$userStatementMock);

        $userRepository = new SqliteUsersRepository($connectionStub);

        $postRepository = new SqlitePostsRepository($connectionStub,$userRepository);

        $this->assertInstanceOf(Post::class,$postRepository->getByTitle('TestTitle'));


    }



}