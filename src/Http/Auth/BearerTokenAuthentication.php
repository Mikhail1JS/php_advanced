<?php

namespace Project\Api\Http\Auth;

use DateTimeImmutable;
use Project\Api\Blog\Exceptions\AuthException;
use Project\Api\Blog\Exceptions\AuthTokenNotFoundException;
use Project\Api\Blog\Exceptions\HttpException;
use Project\Api\Blog\Repositories\AuthTokenRepository\AuthTokensRepositoryInterface;
use Project\Api\Blog\Repositories\UsersRepositories\UsersRepositoryInterface;
use Project\Api\Blog\User;
use Project\Api\Http\Request;

class BearerTokenAuthentication implements TokenAuthenticationInterface
{

    private const HEADER_PREFIX = 'Bearer';

    public function __construct (
        private AuthTokensRepositoryInterface $authTokensRepository,
        private UsersRepositoryInterface $usersRepository
    )
    { }

    /**
     * @throws AuthException
     */
    public function user (Request $request): User {

        try {
            $header = $request->header('Authorization');
        }catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        if(!str_starts_with($header, self::HEADER_PREFIX)){
            throw new AuthException("Malformed token: ($header)");
        }

        $token = trim(mb_substr($header, strlen(self::HEADER_PREFIX)));

        try {
            $authToken = $this->authTokensRepository->get($token);
        }catch(AuthTokenNotFoundException){
            throw new AuthException("Bad token: ($token)");
        }

        if($authToken->expiresOn() <= new DateTimeImmutable()) {
            throw new AuthException("Token expired: ($token)");
        }

        $userUuid = $authToken->userUuid();

        return $this->usersRepository->get($userUuid);

    }

}