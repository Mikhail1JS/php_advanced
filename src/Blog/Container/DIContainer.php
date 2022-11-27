<?php

namespace Project\Api\Blog\Container;

use Project\Api\Blog\Exceptions\NotFoundException;

class DIContainer
{
    public function get (string $type): object
    {
        throw new NotFoundException("Cannot resolve type: $type");

    }

}