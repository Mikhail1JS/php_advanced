<?php

namespace Project\Api\Person;

class Name
{

    public function __construct
    (
     private string $first,
     private string $last
    )
    {

    }

    /**
     * @return string
     */
    public function first(): string
    {
        return $this->first;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->first = $firstName;
    }

    /**
     * @return string
     */
    public function last(): string
    {
        return $this->last;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->last = $lastName;
    }



    public function __toString()
    {
        return $this->first() . ' ' . $this->last();
    }
}