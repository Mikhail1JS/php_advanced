<?php

namespace Project\Api\Http\Auth;

use Project\Api\Blog\User;
use Project\Api\Http\Request;

interface AuthenticationInterface
{
    public function user(Request $request): User;
}