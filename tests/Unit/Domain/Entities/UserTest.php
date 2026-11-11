<?php

declare(strict_types=1);

namespace Domain\Entities;

use App\Domain\Entities\User;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\UserId;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase {
    private UserId $id;

    private DateTimeValue $createdAt;

    public function testConstructorAndGetters() : void {
        $user = new User(
            $this->id,
            'Anna',
            'Andersson',
            1,
            $this->createdAt,
            null,
        );

        self::assertSame($this->id, $user->getId());
        self::assertSame('Anna', $user->getFirstName());
        self::assertSame('Andersson', $user->getLastName());
        self::assertSame(1, $user->getActive());
        self::assertSame($this->createdAt, $user->getCreatedAt());
        self::assertNull($user->getUpdatedAt());
    }

    public function testSetters() : void {
        $user = new User(
            $this->id,
            'Anna',
            'Andersson',
            1,
            $this->createdAt,
            null,
        );

        $user->setFirstName('Bertil');
        $user->setLastName('Bengtsson');
        $user->setActive(0);
        $user->setUpdatedAt(new DateTimeValue('2026-07-15 10:00:00'));

        self::assertSame('Bertil', $user->getFirstName());
        self::assertSame('Bengtsson', $user->getLastName());
        self::assertSame(0, $user->getActive());
        self::assertSame('2026-07-15 10:00:00', $user->getUpdatedAt()->toString());
    }

    public function testFromDBRow() : void {
        $row = [
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'first_name' => 'Anna',
            'last_name' => 'Andersson',
            'active' => 1,
            'created_at' => '2026-07-15 10:00:00',
            'updated_at' => null,
        ];

        $user = User::fromDBRow($row);

        self::assertSame('550e8400-e29b-41d4-a716-446655440000', $user->getId()->toString(), );
        self::assertSame('Anna', $user->getFirstName());
        self::assertSame('Andersson', $user->getLastName());
        self::assertSame(1, $user->getActive());
        self::assertSame('2026-07-15 10:00:00', $user->getCreatedAt()->toString());
        self::assertNull($user->getUpdatedAt());
    }

    public function testAsDBRow() : void {
        $user = new User(
            $this->id,
            'Anna',
            'Andersson',
            1,
            $this->createdAt,
            null,
        );

        $row = $user->asDBRow();

        self::assertSame('550e8400-e29b-41d4-a716-446655440000', $row['id']);
        self::assertSame('Anna', $row['first_name']);
        self::assertSame('Andersson', $row['last_name']);
        self::assertSame(1, $row['active']);
        self::assertSame('2026-07-15 10:00:00', $row['created_at']);
        self::assertNull($row['updated_at']);
    }

    public function testJsonSerialize() : void {
        $user = new User(
            $this->id,
            'Anna',
            'Andersson',
            1,
            $this->createdAt,
            null,
        );

        $data = $user->jsonSerialize();

        self::assertSame('550e8400-e29b-41d4-a716-446655440000', $data['id']);
        self::assertSame('Anna', $data['firstName']);
        self::assertSame('Andersson', $data['lastName']);
        self::assertSame(1, $data['active']);
        self::assertSame('2026-07-15 10:00:00', $data['createdAt']);
        self::assertNull($data['updatedAt']);
    }

    protected function setUp() : void {
        $this->id = new UserId(
            '550e8400-e29b-41d4-a716-446655440000',
        );

        $this->createdAt = new DateTimeValue(
            '2026-07-15 10:00:00',
        );
    }
}
