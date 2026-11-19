<?php

declare(strict_types=1);

namespace App\Application\Commands\TimeEntry;

use App\Domain\Enums\TimeEntryStatus;
use App\Domain\ValueObjects\DateValue;
use App\Domain\ValueObjects\UserId;

class SearchTimeEntriesCommand {
    /**
     * @param array<UserId>|null $users
     */
    public function __construct(
        public int $firstRecord,
        public int $maxRecords,
        public ?array $users,
        public ?DateValue $from,
        public ?DateValue $to,
        public ?TimeEntryStatus $status,
        public ?string $activityRole,
        public string $sortBy,
        public string $sortDirection,
    ) {
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromRequest(
        array $data,
    ) : self {
        $users = null;

        if (isset($data['users'])) {
            $users = array_map(
                static fn (string $id) : UserId => new UserId($id),
                $data['users'],
            );
        }

        return new self(
            firstRecord: (int)($data['firstRecord'] ?? 0),
            maxRecords: (int)($data['maxRecords'] ?? 50),
            users: $users,
            from: isset($data['from'])
                ? new DateValue($data['from'])
                : null,
            to: isset($data['to'])
                ? new DateValue($data['to'])
                : null,
            status: isset($data['status'])
                ? TimeEntryStatus::from($data['status'])
                : null,
            activityRole: $data['activityRole'] ?? null,
            sortBy: $data['sortBy'] ?? 'date',
            sortDirection: $data['sortDirection'] ?? 'asc',
        );
    }
}
