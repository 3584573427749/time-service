<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Application\Commands\User\UpsertUserCommand;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\UserId;
use JsonSerializable;

class User implements JsonSerializable {
    public function __construct(
        private UserId $id,
        private string $firstName,
        private string $lastName,
        private int $active,
        private DateTimeValue $createdAt,
        private ?DateTimeValue $updatedAt,
    ) {
    }

    public function getCreatedAt() : DateTimeValue {
        return $this->createdAt;
    }

    public function getFirstName() : string {
        return $this->firstName;
    }

    public function getLastName() : string {
        return $this->lastName;
    }

    public function getId() : UserId {
        return $this->id;
    }

    public function getActive() : int {
        return $this->active;
    }

    public function getUpdatedAt() : ?DateTimeValue {
        return $this->updatedAt;
    }

    public function setFirstName(string $firstName) : void {
        $this->firstName = $firstName;
    }

    public function setLastName(string $lastName) : void {
        $this->lastName = $lastName;
    }

    public function setActive(int $active) : void {
        $this->active = $active;
    }

    public function setUpdatedAt(?DateTimeValue $updatedAt) : void {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @param array<string, mixed> $row
     */
    public static function fromDBRow(array $row) : self {
        return new self(
            new UserId($row['id']),
            $row['first_name'],
            $row['last_name'],
            $row['active'],
            new DateTimeValue($row['created_at']),
            !empty($row['updated_at']) ? new DateTimeValue($row['updated_at']) : null,
        );
    }

    public static function fromCommand(UpsertUserCommand $command) : self {
        // fromCommand används endast vid skapande av ny användare
        // därför sätts createdAt till nuvarande tid
        // och updatedAt till null
        return new self(
            $command->id,
            $command->firstName,
            $command->lastName,
            $command->active,
            new DateTimeValue('now'),
            null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function asDBRow() : array {
        return [
            'id' => $this->id->toString(),
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'active' => $this->active,
            'created_at' => $this->createdAt->toString(),
            'updated_at' => $this->updatedAt ? $this->updatedAt->toString() : null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize() : array {
        return [
            'id' => $this->id->toString(),
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'active' => $this->active,
            'createdAt' => $this->createdAt->toString(),
            'updatedAt' => $this->updatedAt ? $this->updatedAt->toString() : null,
        ];
    }
}
