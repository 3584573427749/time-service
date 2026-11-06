<?php

declare(strict_types=1);

namespace App\Domain\Handlers;

use App\Domain\Commands\TimeEntryCommand;
use App\Domain\DataTransportObjects\TimeEntryDTO;
use App\Domain\Entities\TimeEntry;
use App\Domain\Exception\FutureDateNotAllowedException;
use App\Domain\Repositories\TimeEntryRepository;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\TimeEntryId;

class AddTimeEntryHandler {
    public function __construct(private TimeEntryRepository $repository) {
    }

    public function handle(TimeEntryCommand $command) : TimeEntryDTO {
        // Affärsregler
        if ($command->date->toString() > date('Y-m-d')) {
            throw new FutureDateNotAllowedException(
                'Tidsposter kan inte registreras för framtida datum',
            );
        }

        $timeEntry = new TimeEntry(
            new TimeEntryId(),
            $command->userId,
            $command->date,
            $command->duration,
            $command->activityRole,
            $command->groupId,
            $command->comment,
            null,
            null,
            null,
            null,
            new DateTimeValue('now'),
            null,
        );

        $this->repository->add($timeEntry);

        $dto = TimeEntryDTO::fromEntity($timeEntry);

        return $dto;
    }
}
