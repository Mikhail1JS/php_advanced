<?php

namespace Project\Api\Blog;

use Project\Api\Person\Name;

class User
{
    /**
     * @param UUID $uuid
     * @param string $username
     * @param Name $name
     */
    public function __construct
    (
        private UUID $uuid,
        private string $username,
        private Name $name,
    ){ }

    /**
     * @return string
     */
    public function __toString(): string
    {

        return "User with id {$this->uuid()} : Name - {$this->getFirstName()} and Lastname - {$this->getLastName()} ";
    }

    /**
     * @param UUID $uuid
     * @return void
     */
    public function setId(UUID $uuid): void
    {
        $this->uuid = $uuid;
    }

    /**
     * @return string
     */
    public function uuid(): string
    {
        return $this->uuid;
    }


    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->name->first();
    }

    /**
     * @param Name $name
     * @return void
     */
    public function setName(Name $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->name->last();
    }

    /**
     * @return string
     */
    public function username(): string
    {
        return $this->username;
    }

    /**
     * @param string $userName
     */
    public function setUserName(string $userName): void
    {
        $this->username = $userName;
    }


    public function name(): string {
       return $this->name;
    }

}