<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers\Group;

use App\Application\Commands\User\UpsertUserCommand;
use App\Application\Handlers\User\UpsertUserHandler;
use App\Domain\DataTransportObjects\UserDTO;
use App\Domain\Entities\User;
use App\Domain\Repositories\UserRepository;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\UserId;
use PHPUnit\Framework\TestCase;

final class UpsertUserHandlerTest extends TestCase {
    public function testHandleUpdatesUser() : void {
        $repository = $this->createMock(UserRepository::class);

        $user = $this->createUser();

        $command = UpsertUserCommand::fromRequest([
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'firstName' => 'Bertil',
            'lastName' => 'Bertilsson',
            'active' => 0,
        ]);

        $repository
            ->expects(self::once())
            ->method('getById')
            ->with($command->id)
            ->willReturn($user);

        $repository
            ->expects(self::once())
            ->method('update')
            ->with(
                self::callback(function (User $user) {
                    return $user->getFirstName() === 'Bertil'
                        && $user->getLastName() === 'Bertilsson'
                        && $user->getActive() === 0;
                }),
            );

        $handler = new class($repository) extends UpsertUserHandler {
            public function __construct(UserRepository $repository) {
                $this->repository = $repository;
            }
        };

        $result = $handler->handle($command);

        self::assertInstanceOf(UserDTO::class, $result);

        $json = $result->jsonSerialize();

        self::assertSame('Bertil', $json['firstName']);
        self::assertSame('Bertilsson', $json['lastName']);
        self::assertSame(0, $json['active']);
        self::assertNotNull($json['updatedAt']);
    }

    public function testHandleCreatesUser() : void {
        $repository = $this->createMock(UserRepository::class);

        $command = UpsertUserCommand::fromRequest([
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'firstName' => 'Bertil',
            'lastName' => 'Bertilsson',
            'active' => 0,
        ]);

        $repository
            ->expects(self::once())
            ->method('getById')
            ->with($command->id)
            ->willReturn(false);

        $repository
            ->expects(self::once())
            ->method('create')
            ->with(
                self::callback(function (User $user) {
                    return $user->getFirstName() === 'Bertil'
                        && $user->getLastName() === 'Bertilsson'
                        && $user->getActive() === 0;
                }),
            );

        $handler = new class($repository) extends UpsertUserHandler {
            public function __construct(UserRepository $repository) {
                $this->repository = $repository;
            }
        };

        $result = $handler->handle($command);

        self::assertInstanceOf(UserDTO::class, $result);

        $json = $result->jsonSerialize();

        self::assertSame('Bertil', $json['firstName']);
        self::assertSame('Bertilsson', $json['lastName']);
        self::assertSame(0, $json['active']);
        self::assertNull($json['updatedAt']);
    }

    private function createUser() : User {
        return new User(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
            'Anna',
            'Andersson',
            1,
            new DateTimeValue('2026-01-01 10:00:00'),
            null,
        );
    }
}
