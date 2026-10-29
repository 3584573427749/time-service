<?php

declare(strict_types=1);

namespace App\Application\Commands\User;

use App\Domain\ValueObjects\UserId;

class UpsertUserCommand {
    public function __construct(
        public UserId $id,
        public string $firstName,
        public string $lastName,
        public int $active,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromRequest(array $data) : self {
        return new self(
            new UserId($data['id']),
            $data['firstName']
                |> trim(...)
                |> mb_strtolower(...)
                |> mb_ucfirst(...),
            $data['lastName']
                |> trim(...)
                |> mb_strtolower(...)
                |> mb_ucfirst(...),
            (int)filter_var($data['active'], FILTER_VALIDATE_INT),
        );
    }
}
