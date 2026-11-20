<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Database;

use App\Application\Commands\TimeEntry\SearchTimeEntriesCommand;
use App\Domain\Enums\TimeEntryStatus;
use App\Domain\ValueObjects\DateValue;
use App\Domain\ValueObjects\UserId;
use App\Infrastructure\Database\SearchTimeEntriesQueryBuilder;
use PHPUnit\Framework\TestCase;

final class SearchTimeEntriesQueryBuilderTest extends TestCase {
    public function testBuildWithoutFilters() : void {
        $query = (new SearchTimeEntriesQueryBuilder())
            ->build(
                new SearchTimeEntriesCommand(
                    0,
                    50,
                    null,
                    null,
                    null,
                    null,
                    null,
                    'date',
                    'asc',
                ),
            );

        self::assertSame('', $query->whereClause());

        self::assertSame([], $query->parameters());
    }

    public function testBuildWithUsersFilter() : void {
        $query = (new SearchTimeEntriesQueryBuilder())
            ->build(
                new SearchTimeEntriesCommand(
                    0,
                    50,
                    [
                        new UserId(
                            '550e8400-e29b-41d4-a716-446655440000',
                        ),
                        new UserId(
                            '550e8400-e29b-41d4-a716-446655440001',
                        ),
                    ],
                    null,
                    null,
                    null,
                    null,
                    'date',
                    'asc',
                ),
            );

        self::assertSame(' WHERE user_id IN (?,?)', $query->whereClause());

        self::assertSame(
            [
                '550e8400-e29b-41d4-a716-446655440000',
                '550e8400-e29b-41d4-a716-446655440001',
            ],
            $query->parameters(),
        );
    }

    public function testBuildWithDateFilter() : void {
        $query = (new SearchTimeEntriesQueryBuilder())
            ->build(
                new SearchTimeEntriesCommand(
                    0,
                    50,
                    null,
                    new DateValue('2026-11-01'),
                    new DateValue('2026-11-30'),
                    null,
                    null,
                    'date',
                    'asc',
                ),
            );

        self::assertSame(' WHERE date >= ? AND date <= ?', $query->whereClause());

        self::assertSame(
            [
                '2026-11-01',
                '2026-11-30',
            ],
            $query->parameters(),
        );
    }

    public function testBuildWithOpenStatusFilter() : void {
        $query = (new SearchTimeEntriesQueryBuilder())
            ->build(
                new SearchTimeEntriesCommand(
                    0,
                    50,
                    null,
                    null,
                    null,
                    TimeEntryStatus::OPEN,
                    null,
                    'date',
                    'asc',
                ),
            );

        self::assertSame(' WHERE approved_by IS NULL', $query->whereClause());
    }

    public function testBuildWithApprovedStatusFilter() : void {
        $query = (new SearchTimeEntriesQueryBuilder())
            ->build(
                new SearchTimeEntriesCommand(
                    0,
                    50,
                    null,
                    null,
                    null,
                    TimeEntryStatus::APPROVED,
                    null,
                    'date',
                    'asc',
                ),
            );

        self::assertStringContainsString('approved_by IS NOT NULL', $query->whereClause());

        self::assertStringContainsString('exported_by IS NULL', $query->whereClause());
    }

    public function testBuildWithExportedStatusFilter() : void {
        $query = (new SearchTimeEntriesQueryBuilder())
            ->build(
                new SearchTimeEntriesCommand(
                    0,
                    50,
                    null,
                    null,
                    null,
                    TimeEntryStatus::EXPORTED,
                    null,
                    'date',
                    'asc',
                ),
            );

        self::assertSame(' WHERE exported_by IS NOT NULL', $query->whereClause());
    }

    public function testBuildWithActivityRoleFilter() : void {
        $query = (new SearchTimeEntriesQueryBuilder())
            ->build(
                new SearchTimeEntriesCommand(
                    0,
                    50,
                    null,
                    null,
                    null,
                    null,
                    'Ledare',
                    'date',
                    'asc',
                ),
            );

        self::assertSame(' WHERE activity_role = ?', $query->whereClause());

        self::assertSame(['Ledare', ], $query->parameters());
    }

    public function testOrderByClauseUsesDate() : void {
        $query = new SearchTimeEntriesQueryBuilder();

        $command = new SearchTimeEntriesCommand(
            0,
            50,
            null,
            null,
            null,
            null,
            null,
            'date',
            'asc',
        );

        self::assertSame(' ORDER BY date ASC', $query->orderByClause($command));
    }

    public function testPagingClause() : void {
        $query = new SearchTimeEntriesQueryBuilder();

        $command = new SearchTimeEntriesCommand(
            100,
            25,
            null,
            null,
            null,
            null,
            null,
            'date',
            'asc',
        );

        self::assertSame(' LIMIT 25 OFFSET 100', $query->pagingClause($command));
    }

    public function testOrderByClauseUsesUser() : void {
        $query = new SearchTimeEntriesQueryBuilder();

        $command = new SearchTimeEntriesCommand(
            0,
            50,
            null,
            null,
            null,
            null,
            null,
            'user',
            'asc',
        );

        self::assertSame(' ORDER BY user_id ASC', $query->orderByClause($command));
    }

    public function testOrderByClauseUsesStatus() : void {
        $query = new SearchTimeEntriesQueryBuilder();

        $command = new SearchTimeEntriesCommand(
            0,
            50,
            null,
            null,
            null,
            null,
            null,
            'status',
            'desc',
        );

        $orderBy = $query->orderByClause($command);

        self::assertStringContainsString('CASE', $orderBy);

        self::assertStringContainsString('approved_by IS NULL', $orderBy);

        self::assertStringContainsString('exported_by IS NULL', $orderBy);

        self::assertStringEndsWith('DESC', $orderBy);
    }
}
