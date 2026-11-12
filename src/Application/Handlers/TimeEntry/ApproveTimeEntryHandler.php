<?php

declare(strict_types=1);

namespace App\Application\Handlers\TimeEntry;

use App\Application\Security\CurrentUser;
use App\Domain\DataTransportObjects\TimeEntryDTO;
use App\Domain\Exception\TimeEntryAlreadyApprovedException;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\TimeEntryId;

class ApproveTimeEntryHandler extends TimeEntryHandler {
    public function handle(
        TimeEntryId $id,
        CurrentUser $currentUser,
    ) : TimeEntryDTO {
        $timeEntry = $this->repository->get($id);

        if ($timeEntry->getApprovedAt() !== null) {
            throw new TimeEntryAlreadyApprovedException(
                'Tidsposten är redan attesterad',
            );
        }

        $timeEntry->approve(
            $currentUser->getUserId(),
            new DateTimeValue('now'),
        );

        $this->repository->update($timeEntry);

        return TimeEntryDTO::fromEntity($timeEntry);
    }
}
