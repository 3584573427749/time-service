<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers\TimeEntry;

use App\Application\Commands\TimeEntry\SearchTimeEntriesCommand;
use App\Application\Handlers\TimeEntry\SearchTimeEntriesHandler;
use App\Domain\DataTransportObjects\SearchTimeEntriesDTO;
use App\Domain\Entities\TimeEntry;
use App\Domain\Repositories\TimeEntryRepository;
use PHPUnit\Framework\TestCase;

final class SearchTimeEntriesHandlerTest extends TestCase {
    public function testHandleReturnsSearchResult() : void {
        $repository = $this->createMock(TimeEntryRepository::class);

        $entry = $this->createMock(TimeEntry::class);

        $command = SearchTimeEntriesCommand::fromRequest(['firstRecord' => 25, ]);

        $repository
            ->expects($this->once())
            ->method('search')
            ->with($command)
            ->willReturn([
                $entry,
            ]);

        $repository
            ->expects($this->once())
            ->method('getTotalCount')
            ->with($command)
            ->willReturn(
                123,
            );

        $handler = new SearchTimeEntriesHandler($repository, );

        $result = $handler->handle($command);

        self::assertInstanceOf(SearchTimeEntriesDTO::class, $result);

        self::assertSame(25, $result->firstRecord);

        self::assertCount(1, $result->timeEntries);

        self::assertSame(123, $result->totalCount);
    }
}
