<?php

declare(strict_types=1);

namespace Application\Commands\User;

use App\Application\Commands\User\UpsertUserCommand;
use PHPUnit\Framework\TestCase;

final class UpsertUserCommandTest extends TestCase {
    public function testFromRequestMapsValuesCorrectly() : void {
        $data = [
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'firstName' => 'Adam',
            'lastName' => 'Andersson',
            'active' => 1,
        ];

        $command = UpsertUserCommand::fromRequest($data);

        self::assertSame('550e8400-e29b-41d4-a716-446655440000', $command->id->toString());
        self::assertSame('Adam', $command->firstName);
        self::assertSame('Andersson', $command->lastName);
        self::assertSame(1, $command->active);
    }

    public function testNameIsTrimmedAndNormalized() : void {
        $data = [
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'firstName' => '  aDam  ',
            'lastName' => '  aNdersson  ',
            'active' => 1,
        ];

        $command = UpsertUserCommand::fromRequest($data);

        self::assertSame('Adam', $command->firstName);
        self::assertSame('Andersson', $command->lastName);
    }
}
