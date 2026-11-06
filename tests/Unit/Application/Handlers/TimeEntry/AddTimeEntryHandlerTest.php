<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers\TimeEntry;

use App\Application\Commands\TimeEntry\TimeEntryCommand;
use App\Application\Handlers\TimeEntry\AddTimeEntryHandler;
use App\Domain\DataTransportObjects\TimeEntryDTO;
use App\Domain\Exception\FutureDateNotAllowedException;
use App\Domain\Repositories\TimeEntryRepository;
use PHPUnit\Framework\TestCase;

final class AddTimeEntryHandlerTest extends TestCase {
    public function testCreatesTimeEntryAndReturnsDto() : void {
        $repository = $this->createMock(TimeEntryRepository::class);

        $repository
            ->expects($this->once())
            ->method('add');

        $handler = new AddTimeEntryHandler($repository);

        $command = TimeEntryCommand::fromRequest([
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'date' => date('Y-m-d'),
            'duration' => '03:05',
            'activityRole' => 'Ledare',
            'groupId' => '660e8400-e29b-41d4-a716-446655440000',
            'comment' => 'Testkommentar',
        ]);
        $result = $handler->handle($command);

        self::assertInstanceOf(
            TimeEntryDTO::class,
            $result,
        );
    }

    public function testThrowsExceptionWhenDateIsInFuture() : void {
        $repository = $this->createMock(TimeEntryRepository::class);

        $repository
            ->expects($this->never())
            ->method('add');

        $handler = new AddTimeEntryHandler($repository);

        $command = TimeEntryCommand::fromRequest([
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'date' => date('Y-m-d', strtotime('next week')),
            'duration' => '03:05',
            'activityRole' => 'Ledare',
            'groupId' => '660e8400-e29b-41d4-a716-446655440000',
            'comment' => 'Testkommentar',
        ]);
        ;

        $this->expectException(
            FutureDateNotAllowedException::class,
        );

        $handler->handle($command);
    }
}
