<?php
use Project\Api\Person\User;
use Project\Api\Post\Comment;
use Project\Api\Post\Post;

require __DIR__.'/vendor/autoload.php';

//spl_autoload_register('load');
//
//function load ($classname)
//{
//
//    $file = str_replace('_',DIRECTORY_SEPARATOR,$classname) . ".php";
//    $file = str_replace(['\\','Project\Api'], [DIRECTORY_SEPARATOR,'src'],$file);
//
//    if(file_exists($file)){
//        require $file;
//    }
//}

$faker = Faker\Factory::create('en_US');


if($argv[1] === 'user') {
    $user = new User ($faker->randomDigitNotNull(),$faker->firstName('male'),$faker->lastName());
    echo $user;
}elseif ($argv[1] === 'post'){
    $user = new User ($faker->randomDigitNotNull(),$faker->firstName('male'),$faker->lastName());
    $post = new Post($faker->randomDigitNotNull(),$user,$faker->sentence(), $faker->paragraph());
    echo $post;
}elseif ($argv[1] === 'comment'){
    $user = new User ($faker->randomDigitNotNull(),$faker->firstName('male'),$faker->lastName());
    $post = new Post($faker->randomDigitNotNull(),$user,$faker->sentence(), $faker->paragraph());
    $comment = new Comment($faker->randomDigitNotNull(),$user,$post,$faker->text());
    echo $comment;
}
