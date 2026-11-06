<?php

declare(strict_types=1);

namespace App\Application\Commands\TimeEntry;

use App\Domain\ValueObjects\DateValue;
use App\Domain\ValueObjects\GroupId;
use App\Domain\ValueObjects\TimeEntryId;
use App\Domain\ValueObjects\TimeValue;
use App\Domain\ValueObjects\UserId;

class TimeEntryCommand {
    private function __construct(
        public ?TimeEntryId $id,
        public UserId $userId,
        public DateValue $date,
        public TimeValue $duration,
        public string $activityRole,
        public ?GroupId $groupId,
        public ?string $comment,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromRequest(array $data) : self {
        return new self(
            id:isset($data['id'])
                ? new TimeEntryId($data['id'])
                : null,
            userId:new UserId($data['userId']),
            date:new DateValue($data['date']),
            duration:new TimeValue($data['duration']),
            activityRole:$data['activityRole'],
            groupId:isset($data['groupId'])
                ? new GroupId($data['groupId'])
                : null,
            comment:isset($data['comment'])
                ? $data['comment']
                : null,
        );
    }
}
