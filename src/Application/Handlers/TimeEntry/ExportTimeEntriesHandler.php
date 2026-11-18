<?php

declare(strict_types=1);

namespace App\Application\Handlers\TimeEntry;

use App\Application\Commands\TimeEntry\ExportTimeEntriesCommand;
use App\Application\Security\CurrentUser;
use App\Domain\DataTransportObjects\TimeEntriesExportRow;
use App\Domain\Exception\NotFoundException;
use App\Domain\Repositories\TimeEntryRepository;
use App\Domain\ValueObjects\DateTimeValue;
use Doctrine\DBAL\Connection;
use Throwable;

class ExportTimeEntriesHandler extends TimeEntryHandler {
    public function __construct(TimeEntryRepository $repository, private Connection $connection) {
        parent::__construct($repository);
    }

    /**
     * @throws NotFoundException
     */
    public function handle(ExportTimeEntriesCommand $command, CurrentUser $currentUser) : string {
        $this->connection->beginTransaction();

        try {
            $csv = TimeEntriesExportRow::header();

            $userIds = $command->users
                ?? $this->repository->getUsersWithExportableTimeEntries();

            $exportedAt = new DateTimeValue('now');

            foreach ($userIds as $userId) {
                $entries = $this->repository->getApprovedNotExportedByUser($userId);

                if (count($entries) === 0) {
                    continue;
                }

                foreach ($entries as $entry) {
                    $row = TimeEntriesExportRow::fromTimeEntry($entry);
                    $csv .= $row->toCsvString();

                    $entry->markAsExported($currentUser->getUserId(), $exportedAt);
                    $this->repository->update($entry);
                }
            }

            $this->connection->commit();

            return $csv;
        } catch (Throwable $e) {

            $this->connection->rollBack();

            throw $e;
        }
    }
}
