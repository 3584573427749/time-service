<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers\TimeEntry;

use App\Application\Handlers\TimeEntry\DeleteTimeEntryHandler;
use App\Domain\Repositories\TimeEntryRepository;
use App\Domain\ValueObjects\TimeEntryId;
use PHPUnit\Framework\TestCase;

final class DeleteTimeEntryHandlerTest extends TestCase {
    public function testHandleDeletesTimeEntry() : void {
        $repository = $this->createMock(
            TimeEntryRepository::class,
        );

        $repository
            ->expects($this->once())
            ->method('delete')
            ->with(
                $this->isInstanceOf(
                    TimeEntryId::class,
                ),
            );

        $handler = new DeleteTimeEntryHandler(
            $repository,
        );

        $handler->handle(
            new TimeEntryId(
                '550e8400-e29b-41d4-a716-446655440001',
            ),
        );
    }
}
