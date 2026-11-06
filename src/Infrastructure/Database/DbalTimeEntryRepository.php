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

    public function getByUserId(UserId $userId) : TimeEntry|false {
        $row = $this->connection->executeQuery('SELECT * FROM ' . self::TABLE . ' WHERE user_id = ? ', [$userId->toString()])
                                ->fetchAssociative();

        if ($row === false) {
            return false;
        }

        return TimeEntry::fromDBRow($row);
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
}
