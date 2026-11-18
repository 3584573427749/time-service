<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Domain\Entities\TimeEntry;
use App\Domain\Exception\NotFoundException;
use App\Domain\Repositories\TimeEntryRepository;
use App\Domain\ValueObjects\TimeEntryId;
use App\Domain\ValueObjects\UserId;
use Doctrine\DBAL\Exception;

class DbalTimeEntryRepository extends AbstractDbRepository implements TimeEntryRepository {
    private const TABLE = 'time_entries';

    /**
     * @inheritDoc
     */
    public function getAll() : array {
        $rows = $this->connection->executeQuery('SELECT * FROM ' . self::TABLE)
                                 ->fetchAllAssociative();

        return array_map(fn ($row) => TimeEntry::fromDBRow($row), $rows);
    }

    /**
     * @return TimeEntry[]
     */
    public function getByUser(UserId $userId) : array {
        $rows = $this->connection
            ->executeQuery('SELECT * FROM ' . self::TABLE . ' WHERE user_id = ? ', [$userId->toString()])
            ->fetchAllAssociative();

        return array_map(fn ($row) => TimeEntry::fromDBRow($row), $rows);
    }

    public function get(TimeEntryId $id) : TimeEntry {
        $row = $this->connection->executeQuery('SELECT * FROM ' . self::TABLE . ' WHERE id = ? ', [$id->toString()])
                                ->fetchAssociative();

        if ($row === false) {
            throw new NotFoundException('Tidspost med id ' . $id->toString() . ' hittades inte');
        }

        return TimeEntry::fromDBRow($row);
    }

    /**
     * @throws Exception
     */
    public function delete(TimeEntryId $id) : void {
        $affectedRows = $this->connection->delete(self::TABLE, ['id' => $id->toString()]);

        if ($affectedRows === 0) {
            throw new NotFoundException('TimeEntry saknas');
        }
    }

    public function add(TimeEntry $timeEntry) : void {
        $this->connection->insert(self::TABLE, $timeEntry->asDBRow());
    }

    public function update(TimeEntry $timeEntry) : void {
        $this->connection->update(self::TABLE, $timeEntry->asDBRow(), ['id' => $timeEntry->getId()->toString()]);
    }

    /**
     * @return UserId[]
     * @throws Exception
     */
    public function getUsersWithExportableTimeEntries() : array {
        $userIds = $this->connection
            ->executeQuery('SELECT DISTINCT user_id FROM ' . self::TABLE . ' WHERE approved_by IS NOT NULL AND exported_by IS NULL')
            ->fetchFirstColumn();

        return array_map(fn ($userId) => new UserId($userId), $userIds);
    }

    /**
     * @throws Exception
     */
    public function getApprovedNotExportedByUser(UserId $userId) : array {
        $rows = $this->connection
            ->executeQuery(
                'SELECT * FROM ' . self::TABLE .
                ' WHERE user_id = ? AND approved_by IS NOT NULL AND exported_by IS NOT NULL',
                [$userId->toString()],
            )
            ->fetchAllAssociative();

        return array_map(fn ($row) => TimeEntry::fromDBRow($row), $rows);
    }
}
