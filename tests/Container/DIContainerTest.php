<?php

namespace Project\Api\Tests\Container;

use PHPUnit\Framework\TestCase;
use Project\Api\Blog\Container\DIContainer;
use Project\Api\Blog\Exceptions\NotFoundException;
use Project\Api\Blog\Repositories\UsersRepositories\InMemoryUsersRepository;
use Project\Api\Blog\Repositories\UsersRepositories\UsersRepositoryInterface;

class DIContainerTest extends TestCase
{
public function testItThrowsAnExceptionIfCannotResolveType(): void
{
    $container = new DIContainer();

    $this->expectException(NotFoundException::class);
    $this->expectExceptionMessage("Cannot resolve type: Project\Api\Tests\Container\SomeClass");

    $container->get(SomeClass::class);
}

public function testItResolvesClassWithoutDependencies(): void
{
    $container = new DIContainer();

    $object = $container->get(SomeClassWithoutDependencies::class);

    $this->assertInstanceOf(
        SomeClassWithoutDependencies::class,
        $object);

}

public function testItResolvesClassByContract(): void
{
    $container = new DIContainer();

    $container->bind(
        UsersRepositoryInterface::class,
        InMemoryUsersRepository::class
    );

    $object = $container->get(UsersRepositoryInterface::class);

    $this->assertInstanceOf(InMemoryUsersRepository::class,$object);
}

public function testItReturnsPredefinedObject(): void {

    $container = new DIContainer();

    $container->bind(
        SomeClassWithParameter::class,
        new SomeClassWithParameter(77)
    );

    $object = $container->get(SomeClassWithParameter::class);

    $this->assertInstanceOf(SomeClassWithParameter::class,$object);
    $this->assertSame(77,$object->value());
}

public function testItResolvesClassWithDependencies(): void {

    $container = new DIContainer();

    $container->bind(
        SomeClassWithParameter::class,
        new SomeClassWithParameter(55)
    );

    $object = $container->get(ClassDependingOnAnother::class);

    $this->assertInstanceOf(ClassDependingOnAnother::class,$object);
}

}