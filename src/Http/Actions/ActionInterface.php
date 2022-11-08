<?php

namespace Project\Api\Http\Actions;

use Project\Api\Http\Request;
use Project\Api\Http\Response;

interface ActionInterface
{
    public function handle (Request $request): Response;

}