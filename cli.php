<?php

use Project\Api\Blog\Commands\Arguments;
use Project\Api\Blog\Commands\CreateUserCommand;
use Project\Api\Blog\Exceptions\AppException;
use Psr\Log\LoggerInterface;


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

$container = require __DIR__.'/bootstrap.php';


$command = $container->get(CreateUserCommand::class);
$logger =  $container->get(LoggerInterface::class);


try {
    $command->handle(Arguments::fromArgv($argv));
}catch(AppException $e){
   $logger->error($e->getMessage(),['exception' => $e] );
}
