<?php

namespace Project\Api\Blog\Repositories\AuthTokenRepository;

use Project\Api\Blog\AuthToken;

interface AuthTokensRepositoryInterface
{

    public function save(AuthToken $authToken): void;

    public function get(string $token): AuthToken;

}