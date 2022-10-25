<?php

namespace Repositories;

use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Project\Api\Blog\Comment;
use Project\Api\Blog\Exceptions\CommentNotFoundException;
use Project\Api\Blog\Post;
use Project\Api\Blog\Repositories\CommentsRepositories\SqliteCommentsRepository;
use Project\Api\Blog\Repositories\PostsRepositories\SqlitePostsRepository;
use Project\Api\Blog\Repositories\UsersRepositories\SqliteUsersRepository;
use Project\Api\Blog\User;
use Project\Api\Blog\UUID;
use Project\Api\Person\Name;

class SqlCommentsRepositoryTest extends TestCase
{

    public function testItThrowsAnExceptionWhenCommentNotFound(): void {

        $connectionStub = $this->createMock(PDO::class);

        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock->method('fetch')
            ->willReturn(false);

        $connectionStub->method('prepare')
            ->willReturn($statementMock);


        $userRepository = new SqliteUsersRepository($connectionStub);
        $postRepository = new SqlitePostsRepository($connectionStub, $userRepository);
        $commentsRepository = new SqliteCommentsRepository($connectionStub,$userRepository,$postRepository);

        $this->expectException(CommentNotFoundException::class);
        $this->expectExceptionMessage("Cannot find comment: 69265fe0-6ba4-43b4-85bc-bcedeb31e6ba");

        $commentsRepository->get(new UUID("69265fe0-6ba4-43b4-85bc-bcedeb31e6ba"));
    }

    public function testItSavesCommentToDataBase(): void {

        $connectionMock = $this->createMock(PDO::class);

        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid' => "47653ee5-94fa-482d-9301-8b3e76def3bd",
                ':post_uuid' => "5b9a7d5e-c221-4a91-805e-05b638f596ee",
                ':author_uuid' => "69265fe0-6ba4-43b4-85bc-bcedeb31e6ba",
                ':text' => 'someText']);

        $connectionMock->method('prepare')->willReturn($statementMock);
        $userRepository = new SqliteUsersRepository($connectionMock);
        $postRepository = new SqlitePostsRepository($connectionMock,$userRepository);
        $commentsRepository = new SqliteCommentsRepository($connectionMock,$userRepository,$postRepository);

        $user = new User(new UUID("69265fe0-6ba4-43b4-85bc-bcedeb31e6ba"), "Fire92", new Name('Tom', 'Black'));

        $post = new Post(new UUID("5b9a7d5e-c221-4a91-805e-05b638f596ee"), $user, 'TestTitle', 'SomeText');

        $commentsRepository->save(new Comment(new UUID("47653ee5-94fa-482d-9301-8b3e76def3bd"),$user,$post,'someText') );


    }

    public function testItGetCommentFromDataBase(): void {

        $connectionMock = $this->createMock(PDO::class);

        $commentStatementMock = $this->createMock(PDOStatement::class);
//        $userStatementMock = $this->createMock(PDOStatement::class);
//        $postStatementMock = $this->createMock(PDOStatement::class);



//        $userStatementMock->expects($this->once())
//            ->method('fetch')
//            ->willReturn(
//                [
//                'uuid' => "69265fe0-6ba4-43b4-85bc-bcedeb31e6ba",
//                'username' => 'Fire92',
//                'first_name' => 'John',
//                'last_name'=> 'Black'
//                ]
//            );


//        $postStatementMock->expects($this->once())
//            ->method('fetch')
//            ->willReturn(
//                [
//                'uuid' => "5b9a7d5e-c221-4a91-805e-05b638f596ee",
//                'title' => 'TestTitle',
//                'text' => 'SomeText',
//                'author_uuid' => "69265fe0-6ba4-43b4-85bc-bcedeb31e6ba",
//            ]
//            );

//        $commentStatementMock->expects($this->atLeastOnce())
//            ->method('execute')
//            ->with(
//                [':uuid'=> "47653ee5-94fa-482d-9301-8b3e76def3bd"]
//            );

        $commentStatementMock->expects($this->atLeastOnce())
            ->method('fetch')
            ->willReturn(
                [
                    'uuid' =>"47653ee5-94fa-482d-9301-8b3e76def3bd",
                    'post_uuid' => "5b9a7d5e-c221-4a91-805e-05b638f596ee",
                    'author_uuid' => "69265fe0-6ba4-43b4-85bc-bcedeb31e6ba",
                    'text' => 'SomeText',
                    'title' => 'TestTitle',
                    'username' => 'Fire92',
                    'first_name' => 'John',
                    'last_name'=> 'Black'
                ]
            );

        $connectionMock->method('prepare')->willReturn($commentStatementMock);

        $userRepository = new SqliteUsersRepository($connectionMock);
        $postRepository = new SqlitePostsRepository($connectionMock,$userRepository);
        $commentsRepository = new SqliteCommentsRepository($connectionMock,$userRepository,$postRepository);

        $comment = $commentsRepository->get(new UUID("47653ee5-94fa-482d-9301-8b3e76def3bd"));

        $this->assertInstanceOf(Comment::class,$comment);

    }

}