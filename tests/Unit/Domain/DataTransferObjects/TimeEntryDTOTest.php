<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\DataTransportObjects;

use App\Domain\DataTransportObjects\TimeEntryDTO;
use App\Domain\Entities\TimeEntry;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\DateValue;
use App\Domain\ValueObjects\GroupId;
use App\Domain\ValueObjects\TimeEntryId;
use App\Domain\ValueObjects\TimeValue;
use App\Domain\ValueObjects\UserId;
use PHPUnit\Framework\TestCase;

final class TimeEntryDTOTest extends TestCase {
    public function testCanBeCreatedFromEntity() : void {
        $entity = $this->createTimeEntry();

        $dto = TimeEntryDTO::fromEntity($entity);

        self::assertInstanceOf(
            TimeEntryDTO::class,
            $dto,
        );
    }

    public function testCanBeSerializedToJson() : void {
        $entity = $this->createTimeEntry();

        $dto = TimeEntryDTO::fromEntity($entity);

        $data = $dto->jsonSerialize();

        self::assertSame(
            '550e8400-e29b-41d4-a716-446655440001',
            $data['id'],
        );

        self::assertSame(
            '550e8400-e29b-41d4-a716-446655440000',
            $data['userId'],
        );

        self::assertSame(
            '2026-11-05',
            $data['date'],
        );

        self::assertSame(
            '03:05:00',
            $data['duration'],
        );

        self::assertSame(
            'Ledare',
            $data['activityRole'],
        );

        self::assertSame(
            '660e8400-e29b-41d4-a716-446655440000',
            $data['groupId'],
        );

        self::assertSame(
            'Testkommentar',
            $data['comment'],
        );

        self::assertNull(
            $data['approvedBy'],
        );

        self::assertNull(
            $data['approvedAt'],
        );

        self::assertNull(
            $data['exportedBy'],
        );

        self::assertNull(
            $data['exportedAt'],
        );

        self::assertArrayHasKey(
            'createdAt',
            $data,
        );

        self::assertNull(
            $data['updatedAt'],
        );
    }

    private function createTimeEntry() : TimeEntry {
        return new TimeEntry(
            new TimeEntryId('550e8400-e29b-41d4-a716-446655440001'),
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
            new DateValue('2026-11-05'),
            new TimeValue('03:05:00'),
            'Ledare',
            new GroupId('660e8400-e29b-41d4-a716-446655440000'),
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
