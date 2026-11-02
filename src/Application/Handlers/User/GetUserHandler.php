<?php

declare(strict_types=1);

namespace App\Application\Handlers\User;

use App\Domain\DataTransportObjects\UserDTO;
use App\Domain\Exception\NotFoundException;
use App\Domain\ValueObjects\UserId;

class GetUserHandler extends UserHandler {
    /**
     * @return array<UserDTO>
     */
    public function getAll() : array {

        return array_map(
            fn ($user) => UserDTO::fromEntity($user),
            $this->repository->getAll(),
        );
    }

    /**
     * @throws NotFoundException
     */
    public function getId(UserId $id) : UserDTO {
        $user = $this->repository->getById($id);
        if ($user === false) {
            throw new NotFoundException('User saknas');
        }

        return UserDTO::fromEntity($user);
    }
}
