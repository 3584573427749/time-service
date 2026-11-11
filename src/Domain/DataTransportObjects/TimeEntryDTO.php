<?php

declare(strict_types=1);

namespace App\Domain\DataTransportObjects;

use App\Domain\Entities\TimeEntry;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\DateValue;
use App\Domain\ValueObjects\GroupId;
use App\Domain\ValueObjects\TimeEntryId;
use App\Domain\ValueObjects\TimeValue;
use App\Domain\ValueObjects\UserId;

class TimeEntryDTO implements \JsonSerializable {
    public function __construct(
        private TimeEntryId $id,
        private UserId $userId,
        private DateValue $date,
        private TimeValue $duration,
        private string $activityRole,
        private ?GroupId $groupId,
        private ?string $comment,
        private ?UserId $approvedBy,
        private ?DateTimeValue $approvedAt,
        private ?UserId $exportedBy,
        private ?DateTimeValue $exportedAt,
        private DateTimeValue $createdAt,
        private ?DateTimeValue $updatedAt,
    ) {
    }

    public static function fromEntity(TimeEntry $timeEntry) : self {
        return new self(
            $timeEntry->getId(),
            $timeEntry->getUserId(),
            $timeEntry->getDate(),
            $timeEntry->getDuration(),
            $timeEntry->getActivityRole(),
            $timeEntry->getGroupId(),
            $timeEntry->getComment(),
            $timeEntry->getApprovedBy(),
            $timeEntry->getApprovedAt(),
            $timeEntry->getExportedBy(),
            $timeEntry->getExportedAt(),
            $timeEntry->getCreatedAt(),
            $timeEntry->getUpdatedAt(),
        );
    }

    public function jsonSerialize() : mixed {
        return [
            'id' => $this->id->toString(),
            'userId' => $this->userId->toString(),
            'date' => $this->date->toString(),
            'duration' => $this->duration->toString(),
            'activityRole' => $this->activityRole,
            'groupId' => $this->groupId?->toString(),
            'comment' => $this->comment,
            'approvedBy' => $this->approvedBy?->toString(),
            'approvedAt' => $this->approvedAt?->toISOString(),
            'exportedBy' => $this->exportedBy?->toString(),
            'exportedAt' => $this->exportedAt?->toISOString(),
            'createdAt' => $this->createdAt->toISOString(),
            'updatedAt' => $this->updatedAt?->toISOString(),
        ];
    }
}
