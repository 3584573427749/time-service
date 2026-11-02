<?php

declare(strict_types=1);

namespace App\Application\Handlers\User;

use App\Application\Commands\User\UpsertUserCommand;
use App\Domain\DataTransportObjects\UserDTO;
use App\Domain\Entities\User;
use App\Domain\ValueObjects\DateTimeValue;

class UpsertUserHandler extends UserHandler {
    public function handle(UpsertUserCommand $command) : UserDTO {

        $user = $this->repository->getById($command->id);

        if (!$user) {
            $user = User::fromCommand($command);
            $this->repository->create($user);
        } else {
            if ($command->active === 0) {
                // Ta bort allt som är kopplat till användaren, t.ex. grupper, roller, etc.
            }
            $user->setFirstName($command->firstName);
            $user->setLastName($command->lastName);
            $user->setActive($command->active);
            $user->setUpdatedAt(new DateTimeValue('now'));
            $this->repository->update($user);
        }


        $dto = UserDTO::fromEntity($user);

        return $dto;
    }
}
