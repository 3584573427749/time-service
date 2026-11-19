<?php

declare(strict_types=1);

namespace App\Domain\DataTransportObjects;

use JsonSerializable;

final class SearchTimeEntriesDTO implements JsonSerializable {
    /**
     * @param array<TimeEntryDTO> $timeEntries
     */
    public function __construct(
        public readonly int $firstRecord,
        public readonly array $timeEntries,
        public readonly int $totalCount,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize() : array {
        return [
            'firstRecord' => $this->firstRecord,
            'timeEntries' => $this->timeEntries,
            'totalCount' => $this->totalCount,
        ];
    }
}
