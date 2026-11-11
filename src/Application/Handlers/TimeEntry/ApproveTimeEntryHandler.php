<?php

declare(strict_types=1);

namespace App\Application\Handlers\TimeEntry;

use App\Domain\DataTransportObjects\TimeEntryDTO;
use App\Domain\Exception\NotFoundException;
use App\Domain\Exception\TimeEntryAlreadyApprovedException;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\TimeEntryId;
use App\Domain\ValueObjects\UserId;

class ApproveTimeEntryHandler extends TimeEntryHandler {
    /**
     * @throws NotFoundException|TimeEntryAlreadyApprovedException
     */
    public function handle(
        TimeEntryId $id,
        UserId $currentUser,
    ) : TimeEntryDTO {
        $timeEntry = $this->repository->get($id);

        if ($timeEntry->getApprovedAt() !== null) {
            throw new TimeEntryAlreadyApprovedException(
                'Tidsposten är redan attesterad',
            );
        }

        $timeEntry->approve(
            $currentUser,
            new DateTimeValue('now'),
        );

        $this->repository->update($timeEntry);

        return TimeEntryDTO::fromEntity($timeEntry);
    }
}
