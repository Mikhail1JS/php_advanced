<?php

namespace Project\Api\Blog;

use Project\Api\Person\Name;

class User
{
    /**
     * @param UUID $uuid
     * @param string $username
     * @param string $hashedPassword
     * @param Name $name
     */
    public function __construct
    (
        private UUID $uuid,
        private string $username,
        private string $hashedPassword,
        private Name $name,
    ){ }

    /**
     * @return string
     */
    public function __toString(): string
    {

        return (string) $this->name;
    }


    /**
     * @return string
     */
    public function uuid(): string
    {
        return $this->uuid;
    }

    public function username(): string
    {
        return $this->username;
    }

    public function hashedPassword(): string
    {
        return $this->hashedPassword;
    }

    public function checkPassword(string $password): bool {
        return $this->hashedPassword === self::hash($password, $this->uuid);
    }

    public function name(): string {
        return $this->name;
    }

    public function getFirstName(): string
    {
        return $this->name->first();
    }


    public function getLastName(): string
    {
        return $this->name->last();
    }

    public static function createFrom(
        string $username,
        string $password,
        Name $name
    ): self
    {
        $uuid = UUID::random();
        return new self(
            $uuid,
            $username,
            self::hash($password,$uuid),
            $name
        );
    }

    private static function hash (string $password, UUID $uuid): string {
        return hash('sha256',$password . $uuid );
    }





}