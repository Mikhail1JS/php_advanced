<?php

namespace Project\Api\Http\Actions\Users;

use Project\Api\Blog\Exceptions\AlreadyRegisteredException;
use Project\Api\Blog\Exceptions\HttpException;
use Project\Api\Blog\Repositories\UsersRepositories\UsersRepositoryInterface;
use Project\Api\Blog\User;
use Project\Api\Blog\UUID;
use Project\Api\Http\Actions\ActionInterface;
use Project\Api\Http\ErrorResponse;
use Project\Api\Http\Request;
use Project\Api\Http\Response;
use Project\Api\Http\SuccessfulResponse;
use Project\Api\Person\Name;

class CreateUser implements ActionInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    )
    {
    }

    public function handle(Request $request): Response
    {
        try{
            $newUserUuid = UUID::random();
            $newUser = new User(
                $newUserUuid,
                $request->jsonBodyField('username'),
                new Name(
                    $request->jsonBodyField('first_name'),
                    $request->jsonBodyField('last_name')
                )
            );

            $this->usersRepository->save($newUser);
        } catch (AlreadyRegisteredException | HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse(
            [
                'uuid'=>$newUserUuid
            ]
        );

    }
}