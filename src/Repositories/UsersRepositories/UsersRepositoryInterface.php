<?php

namespace Project\Api\Repositories\UsersRepositories;

use Project\Api\Blog\User;
use Project\Api\Blog\UUID;

interface UsersRepositoryInterface
{
    public function save(User $user):void;
    public function get(UUID $uuid):User;
    public function getByUsername(string $username):User;
}