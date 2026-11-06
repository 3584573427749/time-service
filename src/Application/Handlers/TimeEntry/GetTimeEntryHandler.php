<?php

declare(strict_types=1);

namespace App\Application\Handlers\TimeEntry;

use App\Domain\DataTransportObjects\TimeEntryDTO;
use App\Domain\Exception\NotFoundException;
use App\Domain\ValueObjects\TimeEntryId;

class GetTimeEntryHandler extends TimeEntryHandler {
    /**
     * @throws NotFoundException
     */
    public function handle(TimeEntryId $id):TimeEntryDTO {
        $timeEntry = $this->repository->get($id);

        return TimeEntryDTO::fromEntity($timeEntry);
    }
}