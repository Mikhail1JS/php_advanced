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

    public function testItGetUserFromDataBase(): void {

        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock->expects($this->once())
        ->method('execute')
        ->with([':uuid' => '69265fe0-6ba4-43b4-85bc-bcedeb31e6ba']);

        $statementMock->expects($this->once())
        ->method('fetch')
        ->willReturn([
            'uuid' => '69265fe0-6ba4-43b4-85bc-bcedeb31e6ba',
            'username' => 'Fire92',
            'first_name' => 'John',
            'last_name'=> 'Black'
        ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new SqliteUsersRepository($connectionStub);

        $expectedUser = new User (new UUID('69265fe0-6ba4-43b4-85bc-bcedeb31e6ba'),'Fire92',new Name('John','Black'));

        $this->assertEquals($expectedUser,$repository->get(new UUID('69265fe0-6ba4-43b4-85bc-bcedeb31e6ba')));
    }


    public function testItGetUserByNameFromDataBase(): void {

        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock->expects($this->once())
            ->method('execute')
            ->with([':username' => 'Fire92']);

        $statementMock->expects($this->once())
            ->method('fetch')
            ->willReturn([
                'uuid' => '69265fe0-6ba4-43b4-85bc-bcedeb31e6ba',
                'username' => 'Fire92',
                'first_name' => 'John',
                'last_name'=> 'Black'
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new SqliteUsersRepository($connectionStub);

        $expectedUser = new User (new UUID('69265fe0-6ba4-43b4-85bc-bcedeb31e6ba'),'Fire92',new Name('John','Black'));

        $this->assertEquals($expectedUser,$repository->getByUsername('Fire92'));
    }

}