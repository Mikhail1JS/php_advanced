<?php

namespace Project\Api\Http\Auth;
use DateTimeImmutable;
use Exception;
use Project\Api\Blog\AuthToken;
use Project\Api\Blog\Exceptions\AuthException;
use Project\Api\Blog\Exceptions\InvalidArgumentException;
use Project\Api\Blog\Repositories\AuthTokenRepository\AuthTokensRepositoryInterface;
use Project\Api\Blog\UUID;
use Project\Api\Http\Actions\ActionInterface;
use Project\Api\Http\ErrorResponse;
use Project\Api\Http\Request;
use Project\Api\Http\Response;
use Project\Api\Http\SuccessfulResponse;

class LogIn implements ActionInterface
{
    public function __construct(
        private PasswordAuthenticationInterface $passwordAuthentication,
        private AuthTokensRepositoryInterface $authTokensRepository
    ){}

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function handle(Request $request): Response
    {
       try {
           $user = $this->passwordAuthentication->user($request);
       }catch (AuthException $e) {
           return new ErrorResponse($e->getMessage());
       }

       $authToken = new AuthToken(
           bin2hex(random_bytes(40)),
           new UUID($user->uuid()),
           (new DateTimeImmutable())->modify('+1 day')
       );

       $this->authTokensRepository->save($authToken);


    return new SuccessfulResponse([
        'token' => $authToken->token()
    ]);
    }

}