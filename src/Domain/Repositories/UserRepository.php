<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Entities\User;
use App\Domain\ValueObjects\UserId;

interface UserRepository {
    public function create(User $user) : void;

    public function update(User $user) : void;

    /**
     * @return array<User>
     */
    public function getAll() : array;

    public function getById(UserId $id) : User|false;

    public function delete(UserId $id) : void;
}
