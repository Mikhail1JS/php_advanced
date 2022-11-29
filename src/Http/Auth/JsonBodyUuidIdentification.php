<?php

namespace Project\Api\Http\Auth;

use Project\Api\Blog\Exceptions\AuthException;
use Project\Api\Blog\Exceptions\HttpException;
use Project\Api\Blog\Exceptions\InvalidArgumentException;
use Project\Api\Blog\Exceptions\UserNotFoundException;
use Project\Api\Blog\Repositories\UsersRepositories\UsersRepositoryInterface;
use Project\Api\Blog\User;
use Project\Api\Blog\UUID;
use Project\Api\Http\Request;

class JsonBodyUuidIdentification implements IdentificationInterface
{

    public function __construct(
        private UsersRepositoryInterface $usersRepository
    )
    {}

    /**
     * @throws AuthException
     */
    public function user(Request $request): User
    {
        try {
            $userUuid = new UUID($request->jsonBodyField('user_uuid'));
        }catch (HttpException | InvalidArgumentException $e){
            throw new AuthException ($e->getMessage());
        }

        try {
            return $this->usersRepository->get($userUuid);
        }catch (UserNotFoundException $e){
            throw new AuthException($e->getMessage());
        }
    }
}