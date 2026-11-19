<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Entities\TimeEntry;
use App\Domain\ValueObjects\TimeEntryId;
use App\Domain\ValueObjects\UserId;

interface TimeEntryRepository {
    public function add(TimeEntry $timeEntry) : void;

    public function update(TimeEntry $timeEntry) : void;

    /**
     * @return array<TimeEntry>
     */
    public function getAll() : array;

    public function get(TimeEntryId $id) : TimeEntry;

    /**
     * @return array<TimeEntry>
     */
    public function getByUser(UserId $userId) : array;

    public function delete(TimeEntryId $id) : void;

    /**
     * @return array<UserId>
     */
    public function getUsersWithExportableTimeEntries() : array;

    /**
     * @return array<TimeEntry>
     */
    public function getApprovedNotExportedByUser(UserId $userId) : array;

    public function beginTransaction() : void;

    public function commit() : void;

    public function rollback() : void;
}
