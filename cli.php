<?php

use Project\Api\Blog\User;
use Project\Api\Blog\UUID;
use Project\Api\Person\Name;
use Project\Api\Repositories\UsersRepositories\SqlLiteUsersRepository;

require __DIR__ . '/vendor/autoload.php';

//spl_autoload_register('load');
//
//function load ($classname)
//{
//
//    $file = str_replace('_',DIRECTORY_SEPARATOR,$classname) . "InvalidArgumentException.php";
//    $file = str_replace(['\\','Project\Api'], [DIRECTORY_SEPARATOR,'src'],$file);
//
//    if(file_exists($file)){
//        require $file;
//    }
//}

$faker = Faker\Factory::create('en_US');

$connection = new PDO('sqlite:'.__DIR__.'/blog.sqlite');

$usersRepository = new SqlLiteUsersRepository($connection);
$name = new Name ($faker->firstName(),$faker->lastName());
$user = new User (UUID::random(), $faker->userName(), $name);

try {
   echo $usersRepository->get(new UUID ('b71599ea-dc26-458c-b850-9dac071edb93'));
} catch (Exception $e) {
   echo $e->getMessage();
}


//if ($argv[1] === 'user') {
//    $name = new Name ($faker->firstName(),$faker->lastName());
//    $user = new User ($faker->randomDigitNotNull(), $faker->userName(), $name);
//    echo $user;
//} elseif ($argv[1] === 'post') {
//    $name = new Name ($faker->firstName(),$faker->lastName());
//    $user = new User ($faker->randomDigitNotNull(), $faker->userName(), $name);
//    $post = new Post($faker->randomDigitNotNull(), $user, $faker->sentence(), $faker->paragraph());
//    echo $post;
//} elseif ($argv[1] === 'comment') {
//    $name = new Name ($faker->firstName(),$faker->lastName());
//    $user = new User ($faker->randomDigitNotNull(), $faker->userName(), $name);
//    $post = new Post($faker->randomDigitNotNull(), $user, $faker->sentence(), $faker->paragraph());
//    $comment = new Comment($faker->randomDigitNotNull(), $user, $post, $faker->text());
//    echo $comment;
//}
