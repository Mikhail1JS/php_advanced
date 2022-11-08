<?php

namespace Project\Api\Http;

class ErrorResponse extends Response
{
    protected const SUCCESS = false;

    public function __construct(
        private string $reason = "Something went wrong"
    ){

    }

    protected function payload(): array
    {
       return ['reason'=>$this->reason];
    }

}