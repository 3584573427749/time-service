<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Application\Commands\TimeEntry\SearchTimeEntriesCommand;
use App\Domain\Enums\TimeEntryStatus;

class SearchTimeEntriesQueryBuilder {
    /**
     * @var array<string>
     */
    private array $where = [];

    /**
     * @var array<mixed>
     */
    private array $parameters = [];

    public function build(SearchTimeEntriesCommand $command) : self {
        $this->addUsersFilter($command);
        $this->addDateFilter($command);
        $this->addStatusFilter($command);
        $this->addActivityRoleFilter($command);

        return $this;
    }

    private function addUsersFilter(SearchTimeEntriesCommand $command) : void {
        if ($command->users === null) {
            return;
        }

        $placeholders = $command->users
                |> count(...)
                |> (fn ($x) => array_fill(0, $x, '?'))
                |> (fn ($x) => implode(',', $x));

        $this->where[] = sprintf('user_id IN (%s)', $placeholders);

        foreach ($command->users as $userId) {
            $this->parameters[] = $userId->toString();
        }
    }

    private function addDateFilter(SearchTimeEntriesCommand $command) : void {
        if ($command->from !== null) {
            $this->where[] = 'date >= ?';
            $this->parameters[] = $command->from->toString();
        }

        if ($command->to !== null) {
            $this->where[] = 'date <= ?';
            $this->parameters[] = $command->to->toString();
        }
    }

    private function addStatusFilter(SearchTimeEntriesCommand $command) : void {
        switch ($command->status) {
            case TimeEntryStatus::OPEN:
                $this->where[] = 'approved_by IS NULL';
                break;

            case TimeEntryStatus::APPROVED:
                $this->where[] = 'approved_by IS NOT NULL
                     AND exported_by IS NULL';
                break;

            case TimeEntryStatus::EXPORTED:
                $this->where[] = 'exported_by IS NOT NULL';
                break;

            default:
                break;
        }
    }

    private function addActivityRoleFilter(SearchTimeEntriesCommand $command) : void {
        if ($command->activityRole === null) {
            return;
        }

        $this->where[] = 'activity_role = ?';

        $this->parameters[] = $command->activityRole;
    }

    public function whereClause() : string {
        if ($this->where === []) {
            return '';
        }

        return ' WHERE ' . implode(
            ' AND ',
            $this->where,
        );
    }

    /**
     * @return array<mixed>
     */
    public function parameters() : array {
        return $this->parameters;
    }

    public function orderByClause(SearchTimeEntriesCommand $command) : string {
        $column = match ($command->sortBy) {
            'user' => 'user_id',
            'status' => '
            CASE
                WHEN approved_by IS NULL THEN 1
                WHEN exported_by IS NULL THEN 2
                ELSE 3
            END
        ',
            default => 'date',
        };

        $direction = strtoupper($command->sortDirection);

        return sprintf(' ORDER BY %s %s', $column, $direction);
    }

    public function pagingClause(SearchTimeEntriesCommand $command) : string {
        return sprintf(
            ' LIMIT %d OFFSET %d',
            $command->maxRecords,
            $command->firstRecord,
        );
    }
}
