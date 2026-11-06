<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\DateValue;
use App\Domain\ValueObjects\GroupId;
use App\Domain\ValueObjects\TimeEntryId;
use App\Domain\ValueObjects\TimeValue;
use App\Domain\ValueObjects\UserId;
use JsonSerializable;

class TimeEntry implements JsonSerializable {
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

    public function getId() : TimeEntryId {
        return $this->id;
    }

    public function getUserId() : UserId {
        return $this->userId;
    }

    public function getDate() : DateValue {
        return $this->date;
    }

    public function getDuration() : TimeValue {
        return $this->duration;
    }

    public function getActivityRole() : string {
        return $this->activityRole;
    }

    public function getGroupId() : ?GroupId {
        return $this->groupId;
    }

    public function getComment() : ?string {
        return $this->comment;
    }

    public function getApprovedBy() : ?UserId {
        return $this->approvedBy;
    }

    public function getApprovedAt() : ?DateTimeValue {
        return $this->approvedAt;
    }

    public function getExportedBy() : ?UserId {
        return $this->exportedBy;
    }

    public function getExportedAt() : ?DateTimeValue {
        return $this->exportedAt;
    }

    public function getCreatedAt() : DateTimeValue {
        return $this->createdAt;
    }

    public function getUpdatedAt() : ?DateTimeValue {
        return $this->updatedAt;
    }

    public function setDate(DateValue $date) : void {
        $this->date = $date;
    }

    public function setDuration(TimeValue $duration) : void {
        $this->duration = $duration;
    }

    public function setActivityRole(string $activityRole) : void {
        $this->activityRole = $activityRole;
    }

    public function setGroupId(?GroupId $groupId) : void {
        $this->groupId = $groupId;
    }

    public function setComment(?string $comment) : void {
        $this->comment = $comment;
    }

    public function setUpdatedAt(?DateTimeValue $updatedAt) : void {
        $this->updatedAt = $updatedAt;
    }

    public function approve(UserId $userId, DateTimeValue $approvedAt) : void {
        $this->approvedBy = $userId;
        $this->approvedAt = $approvedAt;
    }

    public function markAsExported(UserId $userId, DateTimeValue $exportedAt) : void {
        $this->exportedBy = $userId;
        $this->exportedAt = $exportedAt;
    }

    /**
     * @param array<string,mixed> $row
     */
    public static function fromDBRow(array $row) : self {
        return new self(
            new TimeEntryId($row['id']),
            new UserId($row['user_id']),
            new DateValue($row['date']),
            new TimeValue($row['duration']),
            $row['activity_role'],
            !empty($row['group_id']) ? new GroupId($row['group_id']) : null,
            $row['comment'],
            !empty($row['approved_by']) ? new UserId($row['approved_by']) : null,
            !empty($row['approved_at']) ? new DateTimeValue($row['approved_at']) : null,
            !empty($row['exported_by']) ? new UserId($row['exported_by']) : null,
            !empty($row['exported_at']) ? new DateTimeValue($row['exported_at']) : null,
            new DateTimeValue($row['created_at']),
            !empty($row['updated_at']) ? new DateTimeValue($row['updated_at']) : null,
        );
    }

    /**
     * @return array<string,mixed>
     */
    public function asDBRow() : array {
        return [
            'id' => $this->id->toString(),
            'user_id' => $this->userId->toString(),
            'date' => $this->date->toString(),
            'duration' => $this->duration->toString(),
            'activity_role' => $this->activityRole,
            'group_id' => $this->groupId?->toString(),
            'comment' => $this->comment,
            'approved_by' => $this->approvedBy?->toString(),
            'approved_at' => $this->approvedAt?->toString(),
            'exported_by' => $this->exportedBy?->toString(),
            'exported_at' => $this->exportedAt?->toString(),
            'created_at' => $this->createdAt->toString(),
            'updated_at' => $this->updatedAt?->toString(),
        ];
    }

    /**
     * @return array<string,mixed>
     */
    public function jsonSerialize() : array {
        return [
            'id' => $this->id->toString(),
            'userId' => $this->userId->toString(),
            'date' => $this->date->toString(),
            'duration' => $this->duration->toString(),
            'activityRole' => $this->activityRole,
            'groupId' => $this->groupId?->toString(),
            'comment' => $this->comment,
            'approvedBy' => $this->approvedBy?->toString(),
            'approvedAt' => $this->approvedAt?->toString(),
            'exportedBy' => $this->exportedBy?->toString(),
            'exportedAt' => $this->exportedAt?->toString(),
            'createdAt' => $this->createdAt->toString(),
            'updatedAt' => $this->updatedAt?->toString(),
        ];
    }
}
