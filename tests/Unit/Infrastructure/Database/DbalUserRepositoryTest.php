<?php

declare(strict_types=1);

namespace Infrastructure\Database;

use App\Domain\Entities\User;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\UserId;
use App\Infrastructure\Database\DbalUserRepository;
use Tests\Unit\Infrastructure\Database\DatabaseBaseTestCase;

final class DbalUserRepositoryTest extends DatabaseBaseTestCase {
    private DbalUserRepository $repository;

    public function testCreatesNewUser() : void {
        $user = $this->createUser();

        $this->repository->create($user);

        $row = $this->connection->fetchAssociative(
            'SELECT * FROM users WHERE id = :id',
            ['id' => $user->getId()->toString()],
        );

        self::assertNotFalse($row);
        self::assertSame('Anna', $row['first_name']);
        self::assertSame('Andersson', $row['last_name']);
        self::assertSame(1, $row['active']);
    }

    public function testSaveUpdatesExistingUser() : void {
        $user = $this->createUser();

        $this->connection->insert(
            'users',
            $user->asDBRow(),
        );

        $user->setFirstName('Bertil');
        $user->setLastName('Bengtsson');
        $user->setActive(0);
        $user->setUpdatedAt(
            new DateTimeValue('2026-01-02 10:00:00'),
        );

        $this->repository->update($user);

        $row = $this->connection->fetchAssociative(
            'SELECT * FROM users WHERE id = :id',
            ['id' => $user->getId()->toString()],
        );

        self::assertNotFalse($row);
        self::assertSame('Bertil', $row['first_name']);
        self::assertSame('Bengtsson', $row['last_name']);
        self::assertSame(0, (int) $row['active']);
    }

    public function testGetAllReturnsEmptyArrayWhenNoUsersExist() : void {
        $result = $this->repository->getAll();

        self::assertSame([], $result);
    }

    public function testGetAllReturnsUsers() : void {
        $this->seed('users', [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'first_name' => 'Anna',
                'last_name' => 'Andersson',
                'active' => 1,
                'created_at' => '2026-01-01 10:00:00',
            ],
            [
                'id' => '660e8400-e29b-41d4-a716-446655440000',
                'first_name' => 'Bertil',
                'last_name' => 'Bengtsson',
                'active' => 0,
                'created_at' => '2026-01-01 10:00:00',
            ],
        ]);

        $result = $this->repository->getAll();

        self::assertCount(2, $result);

        self::assertSame('Anna', $result[0]->getFirstName());
        self::assertSame('Andersson', $result[0]->getLastName());
        self::assertSame(1, $result[0]->getActive());

        self::assertSame('Bertil', $result[1]->getFirstName());
        self::assertSame('Bengtsson', $result[1]->getLastName());
        self::assertSame(0, $result[1]->getActive());
    }

    public function testGetByIdReturnsUser() : void {
        $this->seed('users', [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'first_name' => 'Anna',
                'last_name' => 'Andersson',
                'active' => 1,
                'created_at' => '2026-01-01 10:00:00',
            ],
        ]);

        $result = $this->repository->getById(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
        );

        self::assertInstanceOf(User::class, $result);
        self::assertSame('Anna', $result->getFirstName());
        self::assertSame('Andersson', $result->getLastName());
        self::assertSame(1, $result->getActive());
    }

    public function testGetByIdReturnsFalseWhenUserDoesNotExist() : void {
        $result = $this->repository->getById(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
        );

        self::assertFalse($result);
    }

    public function testDeleteRemovesUser() : void {
        $this->seed('users', [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'first_name' => 'Anna',
                'last_name' => 'Andersson',
                'active' => 1,
            ],
        ]);

        $this->repository->delete(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
        );

        $row = $this->connection->fetchAssociative(
            'SELECT * FROM users WHERE id = :id',
            ['id' => '550e8400-e29b-41d4-a716-446655440000'],
        );

        self::assertFalse($row);
    }

    private function createUser(
        ?DateTimeValue $updatedAt = null,
    ) : User {
        return new User(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
            'Anna',
            'Andersson',
            1,
            new DateTimeValue('2026-06-10T10:00:00+00:00'),
            $updatedAt,
        );
    }

    protected function setUp() : void {
        parent::setUp();

        $this->loadSchema('users');

        $this->repository = new DbalUserRepository(
            $this->connection,
        );
    }
}
