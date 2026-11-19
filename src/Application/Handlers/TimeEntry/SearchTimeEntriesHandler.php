<?php

declare(strict_types=1);

namespace App\Application\Handlers\TimeEntry;

use App\Application\Commands\TimeEntry\SearchTimeEntriesCommand;
use App\Domain\DataTransportObjects\SearchTimeEntriesDTO;
use App\Domain\DataTransportObjects\TimeEntryDTO;
use App\Domain\Entities\TimeEntry;

class SearchTimeEntriesHandler extends TimeEntryHandler {
    public function handle(
        SearchTimeEntriesCommand $command,
    ) : SearchTimeEntriesDTO {
        $entries = $this->repository->search(
            $command,
        );

        $timeEntries = array_map(
            fn (TimeEntry $entry) : TimeEntryDTO =>
            TimeEntryDTO::fromEntity($entry),
            $entries,
        );

        return new SearchTimeEntriesDTO(
            $command->firstRecord,
            $timeEntries,
            $this->repository->getTotalCount(
                $command,
            ),
        );
    }
}
