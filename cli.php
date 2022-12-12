<?php

use Project\Api\Blog\Commands\CreateUser;
use Project\Api\Blog\Commands\FakeData\PopulateDB;
use Project\Api\Blog\Commands\Posts\DeletePost;
use Project\Api\Blog\Commands\Users\UpdateUser;
use Symfony\Component\Console\Application;

$container = require __DIR__ . '/bootstrap.php';

$application = new Application();

$commandClasses = [
    CreateUser::class,
    DeletePost::class,
    UpdateUser::class,
    PopulateDB::class
];

foreach ($commandClasses as $commandClass) {
    $command = $container->get($commandClass);
    $application->add($command);
}

$application->run();



