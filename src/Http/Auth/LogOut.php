<?php

namespace Project\Api\Http\Auth;

use DateTimeImmutable;
use Project\Api\Blog\Exceptions\AuthException;
use Project\Api\Blog\Exceptions\AuthTokenNotFoundException;
use Project\Api\Blog\Exceptions\AuthTokensRepositoryException;
use Project\Api\Blog\Exceptions\HttpException;
use Project\Api\Blog\Repositories\AuthTokenRepository\AuthTokensRepositoryInterface;
use Project\Api\Http\Actions\ActionInterface;
use Project\Api\Http\ErrorResponse;
use Project\Api\Http\Request;
use Project\Api\Http\Response;
use Project\Api\Http\SuccessfulResponse;

class LogOut implements ActionInterface
{
    private const HEADER_PREFIX = 'Bearer';

    public function __construct (
        private AuthTokensRepositoryInterface $authTokensRepository
    )
    {}

    public function handle(Request $request): Response
    {
        try {
            $header = $request->header('Authorization');
        }catch (HttpException $e) {
           return new ErrorResponse($e->getMessage());
        }

        if(!str_starts_with($header, self::HEADER_PREFIX)){
            return new ErrorResponse("Malformed token: ($header)");
        }

        $tokenString = trim(mb_substr($header, strlen(self::HEADER_PREFIX)));

        try {
           $token = $this->authTokensRepository->get($tokenString);
        }catch (AuthTokensRepositoryException | AuthTokenNotFoundException $e){
            return new ErrorResponse($e->getMessage());
        }


        $expiredToken = $token->setExpiresOn(new DateTimeImmutable());

        try {
            $this->authTokensRepository->save($expiredToken);
        }catch(AuthTokensRepositoryException $e){
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse(
            ['token'=>$tokenString]
        );
    }
}