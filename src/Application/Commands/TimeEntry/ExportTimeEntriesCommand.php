<?php

declare(strict_types=1);

namespace App\Application\Commands\TimeEntry;

use App\Domain\ValueObjects\UserId;

final class ExportTimeEntriesCommand {
    /**
     * @param array<UserId>|null $users
     */
    public function __construct(public readonly ?array $users) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromRequest(array $data) : self {
        $users = null;

        if (isset($data['users'])) {
            $users = array_map(
                static fn (string $id) : UserId => new UserId($id),
                $data['users'],
            );
        }

        return new self($users, );
    }
}
