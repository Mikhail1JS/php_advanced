<?php

namespace Project\Api\Person;

class User
{

    /**
     * @param int $id
     * @param string $firstName
     * @param string $lastName
     */

    public function __construct
    (
        private int $id,
        private string $firstName,
        private string $lastName

    ){

    }

    public function __toString(): string
    {

        return "User with id {$this->getId()} : Name - {$this->getFirstName()} and Lastname - {$this->getLastName()} ";
    }

    public function setId (int $id):void {
        $this->id = $id;
    }

    public function getId () :int {
        return $this->id;
    }

    /**
     * @param string $firstName
     * @return void
     */
    public function setFirstName (string $firstName):void {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setLastName(string $lastName):void {
        $this->lastName = $lastName;
    }

    public function getLastName():string {
        return $this->lastName;
    }
}