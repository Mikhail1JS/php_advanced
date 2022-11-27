<?php

namespace Project\Api\Blog\Exceptions;

use Psr\Container\NotFoundExceptionInterface;
use Exception;

class NotFoundException extends Exception implements NotFoundExceptionInterface
{

}