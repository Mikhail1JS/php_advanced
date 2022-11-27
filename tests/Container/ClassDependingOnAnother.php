<?php

namespace Project\Api\Tests\Container;


class ClassDependingOnAnother
{
    public function __construct(
        SomeClassWithoutDependencies $one,
        SomeClassWithParameter $two
    )
    {}

}