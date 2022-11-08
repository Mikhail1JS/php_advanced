<?php

namespace Project\Api\Http\Actions\Users;

use Project\Api\Blog\Exceptions\HttpException;
use Project\Api\Blog\Exceptions\UserNotFoundException;
use Project\Api\Blog\Repositories\UsersRepositories\UsersRepositoryInterface;
use Project\Api\Http\Actions\ActionInterface;
use Project\Api\Http\ErrorResponse;
use Project\Api\Http\Response;
use Project\Api\Http\SuccessfulResponse;

class FindByUserName implements ActionInterface
{

    public function __construct( private UsersRepositoryInterface $userRepository){
    }

    public function handle($request): Response
    {
        try {
            $username = $request->query('username');
        }catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $user = $this->userRepository->getByUsername($username);
        }catch (UserNotFoundException $e) {
            return  new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse(
            [
                'username' => $user->username(),
                'name' => $user->name()
            ]
        );
    }

}