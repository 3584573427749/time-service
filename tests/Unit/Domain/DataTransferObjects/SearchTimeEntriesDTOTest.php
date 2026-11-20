<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\DataTransportObjects;

use App\Domain\DataTransportObjects\SearchTimeEntriesDTO;
use App\Domain\DataTransportObjects\TimeEntryDTO;
use PHPUnit\Framework\TestCase;

final class SearchTimeEntriesDTOTest extends TestCase {
    public function testConstructorSetsProperties() : void {
        $timeEntries = [$this->createMock(TimeEntryDTO::class), ];

        $dto = new SearchTimeEntriesDTO(25, $timeEntries, 123, );

        self::assertSame(25, $dto->firstRecord);

        self::assertSame($timeEntries, $dto->timeEntries);

        self::assertSame(123, $dto->totalCount);
    }

    public function testJsonSerializeReturnsExpectedArray() : void {
        $timeEntries = [$this->createMock(TimeEntryDTO::class), ];

        $dto = new SearchTimeEntriesDTO(25, $timeEntries, 123, );

        self::assertSame(
            [
                'firstRecord' => 25,
                'timeEntries' => $timeEntries,
                'totalCount' => 123,
            ],
            $dto->jsonSerialize(),
        );
    }
}
