<?php

declare(strict_types=1);

namespace App\Application\Handlers\TimeEntry;

use App\Application\Commands\TimeEntry\TimeEntryCommand;
use App\Domain\DataTransportObjects\TimeEntryDTO;
use App\Domain\Exception\FutureDateNotAllowedException;
use App\Domain\Exception\NotFoundException;
use App\Domain\ValueObjects\DateTimeValue;

class UpdateTimeEntryHandler extends TimeEntryHandler {
    /**
     * @throws NotFoundException
     */
    public function handle(TimeEntryCommand $command) : TimeEntryDTO {
        if ($command->date->isFuture()) {
            throw new FutureDateNotAllowedException(
                'Tidsposter kan inte registreras för framtida datum',
            );
        }

        $timeEntry = $this->repository->get($command->id);

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
