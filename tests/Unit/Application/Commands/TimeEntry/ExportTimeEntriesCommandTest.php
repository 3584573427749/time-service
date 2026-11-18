<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Commands\TimeEntry;

use App\Application\Commands\TimeEntry\ExportTimeEntriesCommand;
use App\Domain\ValueObjects\UserId;
use PHPUnit\Framework\TestCase;

final class ExportTimeEntriesCommandTest extends TestCase {
    public function testCreatesCommandWithoutUsers() : void {
        $command = ExportTimeEntriesCommand::fromRequest([]);

        self::assertNull($command->users);
    }

    public function testCreatesCommandWithUsers() : void {
        $command = ExportTimeEntriesCommand::fromRequest([
            'users' => [
                '550e8400-e29b-41d4-a716-446655440000',
                '550e8400-e29b-41d4-a716-446655440001',
            ],
        ]);

        self::assertCount(2, $command->users);

        self::assertContainsOnlyInstancesOf(UserId::class, $command->users);

        self::assertSame('550e8400-e29b-41d4-a716-446655440000', $command->users[0]->toString());

        self::assertSame('550e8400-e29b-41d4-a716-446655440001', $command->users[1]->toString());
    }
}
