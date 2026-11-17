<?php

declare(strict_types=1);

namespace App\Application\Handlers\TimeEntry;

use App\Application\Commands\TimeEntry\TimeEntryCommand;
use App\Application\Security\CurrentUser;
use App\Domain\DataTransportObjects\TimeEntryDTO;
use App\Domain\Exception\ForbiddenException;
use App\Domain\Exception\FutureDateNotAllowedException;
use App\Domain\Exception\NotFoundException;
use App\Domain\Exception\TimeEntryAlreadyApprovedException;
use App\Domain\ValueObjects\DateTimeValue;

class UpdateTimeEntryHandler extends TimeEntryHandler {
    /**
     * @throws NotFoundException
     */
    public function handle(TimeEntryCommand $command, CurrentUser $currentUser) : TimeEntryDTO {
        if ($command->date->isFuture()) {
            throw new FutureDateNotAllowedException(
                'Tidsposter kan inte registreras för framtida datum',
            );
        }

        // Hämta aktuell post
        $timeEntry = $this->repository->get($command->id);

        // Kontrollera behörighet
        if (
            !$currentUser->hasPermission('time.update')
            && !$timeEntry->getUserId()->equals($currentUser->getUserId())
        ) {
            throw new ForbiddenException('Saknar behörighet att uppdatera denna tidspost', );
        }

        // Kontrollera att posten inte attesterats
        if ($timeEntry->getApprovedAt() !== null) {
            throw new TimeEntryAlreadyApprovedException(
                'Attesterade tidsposter kan inte uppdateras',
            );
        }

        $timeEntry->setDate($command->date);
        $timeEntry->setDuration($command->duration);
        $timeEntry->setActivityRole($command->activityRole);
        $timeEntry->setGroupId($command->groupId);
        $timeEntry->setComment($command->comment);
        $timeEntry->setUpdatedAt(new DateTimeValue('now'));

        $this->repository->update($timeEntry);

        return TimeEntryDTO::fromEntity(
            $timeEntry,
        );
    }
}
