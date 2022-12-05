<?php

namespace Project\Api\Http\Auth;

use Project\Api\Blog\Exceptions\AuthException;
use Project\Api\Blog\Exceptions\HttpException;
use Project\Api\Blog\Exceptions\UserNotFoundException;
use Project\Api\Blog\Repositories\UsersRepositories\UsersRepositoryInterface;
use Project\Api\Blog\User;
use Project\Api\Http\Request;

class PasswordAuthentication implements PasswordAuthenticationInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ){}

    /**
     * @throws AuthException
     */
    public function user(Request $request): User
    {
        try {
            $username = $request->jsonBodyField('username');
        }catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        try{
            $user = $this->usersRepository->getByUsername($username);
        }catch (UserNotFoundException $e){
            throw new AuthException($e->getMessage());
        }

        try{
            $password = $request->jsonBodyField('password');
        }catch(HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        if(!$user->checkPassword($password) ) {
            throw new AuthException('Wrong password');
        }

        return $user;

    }
}