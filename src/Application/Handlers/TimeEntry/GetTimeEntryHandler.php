<?php

declare(strict_types=1);

namespace App\Application\Handlers\TimeEntry;

use App\Application\Security\CurrentUser;
use App\Domain\DataTransportObjects\TimeEntryDTO;
use App\Domain\Exception\ForbiddenException;
use App\Domain\Exception\NotFoundException;
use App\Domain\ValueObjects\TimeEntryId;

class GetTimeEntryHandler extends TimeEntryHandler {
    /**
     * @throws NotFoundException
     */
    public function handle(TimeEntryId $id, CurrentUser $currentUser) : TimeEntryDTO {
        $timeEntry = $this->repository->get($id);

        if (
            !$currentUser->hasPermission('time.view')
            && !$timeEntry->getUserId()->equals($currentUser->getUserId())
        ) {
            throw new ForbiddenException('Saknar behörighet att visa denna tidspost', );
        }

        return TimeEntryDTO::fromEntity($timeEntry);
    }
}
