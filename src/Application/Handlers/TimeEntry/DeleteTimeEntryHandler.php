<?php

declare(strict_types=1);

namespace App\Application\Handlers\TimeEntry;

use App\Application\Security\CurrentUser;
use App\Domain\Exception\ForbiddenException;
use App\Domain\Exception\TimeEntryAlreadyApprovedException;
use App\Domain\ValueObjects\TimeEntryId;

class DeleteTimeEntryHandler extends TimeEntryHandler {
    public function handle(TimeEntryId $id, CurrentUser $currentUser) : void {
        $timeEntry = $this->repository->get($id);

        if (
            !$currentUser->hasPermission('time.delete')
            && !$timeEntry->getUserId()->equals(
                $currentUser->getUserId(),
            )
        ) {
            throw new ForbiddenException(
                'Saknar behörighet att radera denna tidspost',
            );
        }

        if ($timeEntry->getApprovedAt() !== null) {
            throw new TimeEntryAlreadyApprovedException(
                'Attesterade tidsposter kan inte raderas',
            );
        }
        $this->repository->delete($id);
    }
}
