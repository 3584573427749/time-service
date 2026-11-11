<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Database;

use App\Domain\Entities\TimeEntry;
use App\Domain\Exception\NotFoundException;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\DateValue;
use App\Domain\ValueObjects\TimeEntryId;
use App\Domain\ValueObjects\TimeValue;
use App\Domain\ValueObjects\UserId;
use App\Infrastructure\Database\DbalTimeEntryRepository;

final class DbalTimeEntryRepositoryTest extends DatabaseBaseTestCase {
    private DbalTimeEntryRepository $repository;

    protected function setUp() : void {
        parent::setUp();

        $this->loadSchema('users');
        $this->loadSchema('time_entries');

        $this->repository = new DbalTimeEntryRepository(
            $this->connection,
        );
    }

    public function testAddCreatesTimeEntry() : void {
        $this->seedUser();

        $this->repository->add(
            $this->createTimeEntry(),
        );

        $count = $this->connection
            ->executeQuery('SELECT COUNT(*) FROM time_entries')
            ->fetchOne();

        self::assertSame(1, (int) $count);
    }

    public function testGetReturnsTimeEntry() : void {
        $this->seed('time_entries', [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440001',
                'user_id' => '550e8400-e29b-41d4-a716-446655440000',
                'date' => '2026-11-05',
                'duration' => '03:05:00',
                'activity_role' => 'Ledare',
                'group_id' => null,
                'comment' => 'Testkommentar',
                'approved_by' => null,
                'approved_at' => null,
                'exported_by' => null,
                'exported_at' => null,
                'created_at' => '2026-11-05 12:00:00',
                'updated_at' => null,
            ],
        ]);

        $timeEntry = $this->repository->get(
            new TimeEntryId('550e8400-e29b-41d4-a716-446655440001'),
        );

        self::assertSame(
            '550e8400-e29b-41d4-a716-446655440001',
            $timeEntry->getId()->toString(),
        );
    }

    public function testGetThrowsNotFoundException() : void {
        $this->expectException(
            NotFoundException::class,
        );

        $this->repository->get(
            new TimeEntryId(),
        );
    }

    public function testGetByUserReturnsTimeEntries() : void {
        $this->seed('time_entries', [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440001',
                'user_id' => '550e8400-e29b-41d4-a716-446655440000',
                'date' => '2026-11-05',
                'duration' => '03:05:00',
                'activity_role' => 'Ledare',
                'group_id' => null,
                'comment' => null,
                'approved_by' => null,
                'approved_at' => null,
                'exported_by' => null,
                'exported_at' => null,
                'created_at' => '2026-11-05 12:00:00',
                'updated_at' => null,
            ],
            [
                'id' => '550e8400-e29b-41d4-a716-446655440002',
                'user_id' => '550e8400-e29b-41d4-a716-446655440000',
                'date' => '2026-11-06',
                'duration' => '01:00:00',
                'activity_role' => 'Assistent',
                'group_id' => null,
                'comment' => null,
                'approved_by' => null,
                'approved_at' => null,
                'exported_by' => null,
                'exported_at' => null,
                'created_at' => '2026-11-06 12:00:00',
                'updated_at' => null,
            ],
        ]);

        $entries = $this->repository->getByUser(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
        );

        self::assertCount(2, $entries);
    }

    public function testGetByUserReturnsEmptyArray() : void {
        $entries = $this->repository->getByUser(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
        );

        self::assertSame([], $entries);
    }

    public function testGetAllReturnsAllTimeEntries() : void {
        $this->seed('time_entries', [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440001',
                'user_id' => '550e8400-e29b-41d4-a716-446655440000',
                'date' => '2026-11-05',
                'duration' => '03:05:00',
                'activity_role' => 'Ledare',
                'group_id' => null,
                'comment' => null,
                'approved_by' => null,
                'approved_at' => null,
                'exported_by' => null,
                'exported_at' => null,
                'created_at' => '2026-11-05 12:00:00',
                'updated_at' => null,
            ],
        ]);

        $entries = $this->repository->getAll();

        self::assertCount(1, $entries);
    }

    public function testUpdateUpdatesTimeEntry() : void {
        $this->seedUser();

        $timeEntry = $this->createTimeEntry();

        $this->repository->add($timeEntry);

        $timeEntry->setActivityRole('Assistent');

        $this->repository->update($timeEntry);

        $updated = $this->repository->get(
            $timeEntry->getId(),
        );

        self::assertSame(
            'Assistent',
            $updated->getActivityRole(),
        );
    }

    public function testDeleteRemovesTimeEntry() : void {
        $this->seedUser();

        $timeEntry = $this->createTimeEntry();

        $this->repository->add($timeEntry);

        $this->repository->delete(
            $timeEntry->getId(),
        );

        $count = $this->connection
            ->executeQuery('SELECT COUNT(*) FROM time_entries')
            ->fetchOne();

        self::assertSame(0, (int) $count);
    }

    public function testDeleteThrowsNotFoundException() : void {
        $this->expectException(
            NotFoundException::class,
        );

        $this->repository->delete(
            new TimeEntryId(),
        );
    }

    private function seedUser() : void {
        $this->seed('users', [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'first_name' => 'Test',
                'last_name' => 'User',
                'active' => 1,
                'created_at' => '2026-01-01 10:00:00',
                'updated_at' => null,
            ],
        ]);
    }

    private function createTimeEntry() : TimeEntry {
        return new TimeEntry(
            new TimeEntryId('550e8400-e29b-41d4-a716-446655440001'),
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
            new DateValue('2026-11-05'),
            new TimeValue('03:05:00'),
            'Ledare',
            null,
            'Testkommentar',
            null,
            null,
            null,
            null,
            new DateTimeValue('2026-11-05T12:00:00+00:00'),
            null,
        );
    }
}
