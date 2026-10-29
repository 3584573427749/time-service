<?php

declare(strict_types=1);

namespace Domain\DataTransferObjects;

use App\Domain\DataTransportObjects\UserDTO;
use App\Domain\Entities\User;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\UserId;
use PHPUnit\Framework\TestCase;

final class UserDTOTest extends TestCase {
    public function testFromEntityCreatesDto() : void {
        $user = new User(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
            'Anna',
            'Andersson',
            1,
            new DateTimeValue('2026-01-01T10:00:00+00:00'),
            null,
        );

        $dto = UserDTO::fromEntity($user);

        $data = $dto->jsonSerialize();

        self::assertSame(
            '550e8400-e29b-41d4-a716-446655440000',
            $data['id'],
        );
        self::assertSame('Anna', $data['firstName']);
        self::assertSame('Andersson', $data['lastName']);
        self::assertSame(1, $data['active']);
        self::assertSame('2026-01-01T10:00:00+00:00', $data['createdAt']);
        self::assertNull($data['updatedAt']);
    }

    public function testJsonSerializeReturnsCorrectStructure() : void {
        $user = new User(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
            'Anna',
            'Andersson',
            1,
            new DateTimeValue('2026-01-01T10:00:00+00:00'),
            null,
        );

        $dto = UserDTO::fromEntity($user);

        $data = $dto->jsonSerialize();

        self::assertArrayHasKey('id', $data);
        self::assertArrayHasKey('firstName', $data);
        self::assertArrayHasKey('lastName', $data);
        self::assertArrayHasKey('active', $data);
        self::assertArrayHasKey('createdAt', $data);
        self::assertArrayHasKey('updatedAt', $data);
    }

    public function testJsonSerializeIncludesUpdatedAt() : void {
        $user = new User(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
            'Anna',
            'Andersson',
            1,
            new DateTimeValue('2026-01-01T10:00:00+00:00'),
            new DateTimeValue('2026-01-02T10:00:00+00:00'),
        );

        $dto = UserDTO::fromEntity($user);

        $data = $dto->jsonSerialize();

        self::assertNotNull($data['updatedAt']);
    }
}
