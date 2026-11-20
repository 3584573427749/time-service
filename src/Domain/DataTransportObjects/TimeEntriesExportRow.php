<?php

declare(strict_types=1);

namespace App\Domain\DataTransportObjects;

use App\Domain\Entities\TimeEntry;

class TimeEntriesExportRow {
    private const separator = ',';

    private function __construct(private TimeEntry $timeEntry) {
    }

    public static function fromTimeEntry(TimeEntry $timeEntry) : self {
        return new self($timeEntry);
    }

    public static function header() : string {
        return implode(self::separator, [
                'userId',
                'date',
                'duration',
                'role',
                'groupId',
            ]) . PHP_EOL;
    }

    public function toCsvString() : string {
        return implode(self::separator, [
                $this->timeEntry->getUserId()->toString(),
                $this->timeEntry->getDate()->toString(),
                $this->timeEntry->getDuration()->toString(),
                $this->timeEntry->getActivityRole(),
                $this->timeEntry->getGroupId()?->toString() ?? '',
            ]) . PHP_EOL;
    }
}
