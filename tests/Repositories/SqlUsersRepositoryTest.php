<?php

namespace Repositories;


use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Project\Api\Blog\Exceptions\InvalidArgumentException;
use Project\Api\Blog\Exceptions\UserNotFoundException;
use Project\Api\Blog\Repositories\UsersRepositories\SqliteUsersRepository;
use Project\Api\Blog\User;
use Project\Api\Blog\UUID;
use Project\Api\Person\Name;

class SqlUsersRepositoryTest extends TestCase
{

    /**
     */
    public function testItThrowsAnExceptionWhenUserNotFound(): void {
        $connectionStub = $this->createStub(PDO::class);

        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false);

        $connectionStub->method('prepare')->willReturn($statementStub);

        $repository = new SqliteUsersRepository($connectionStub);

        $this->expectException(UserNotFoundException::class);

        $this->expectExceptionMessage('Cannot get user: Tom');

        $repository->getByUsername('Tom');
    }

    public function testItSavesUserToDataBase(): void {

        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);


        $statementMock->expects($this->once())
        ->method('execute')
        ->willReturn(true)
        ->with(
            [':uuid' => '8fff3c75-3c1d-432f-ac79-4b8bccdef8a9',
            ':username' => 'userTest',
            ':first_name' => 'Jerry',
            ':last_name' => 'Mouth']
        );

        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new SqliteUsersRepository($connectionStub);

        $repository->save(new User(new UUID('8fff3c75-3c1d-432f-ac79-4b8bccdef8a9'),'userTest', new Name('Jerry','Mouth')));

    }

}