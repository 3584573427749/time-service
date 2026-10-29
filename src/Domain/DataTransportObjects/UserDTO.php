<?php

declare(strict_types=1);

namespace App\Domain\DataTransportObjects;

use App\Domain\Entities\User;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\UserId;

class UserDTO implements \JsonSerializable {
    public function __construct(
        private UserId $id,
        private string $firstName,
        private string $lastName,
        private int $active,
        private DateTimeValue $createdAt,
        private ?DateTimeValue $updatedAt,
    ) {
    }

    public static function fromEntity(User $group) : self {
        return new self(
            $group->getId(),
            $group->getFirstName(),
            $group->getLastName(),
            $group->getActive(),
            $group->getCreatedAt(),
            $group->getUpdatedAt() ?? null,
        );

    }

    public function jsonSerialize() : mixed {
        return [
            'id' => $this->id->toString(),
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'active' => $this->active,
            'createdAt' => $this->createdAt->toISOString(),
            'updatedAt' => $this->updatedAt?->toISOString(),
        ];
    }
}
