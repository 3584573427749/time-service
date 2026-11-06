<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers\User;

use App\Application\Handlers\User\GetUserHandler;
use App\Domain\DataTransportObjects\UserDTO;
use App\Domain\Entities\User;
use App\Domain\Exception\NotFoundException;
use App\Domain\Repositories\UserRepository;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\UserId;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;

final class GetUserHandlerTest extends TestCase {
    public function testGetAllReturnsUserDtos():void {
        $db = $this->createMock(Connection::class);
        $repository = $this->createMock(UserRepository::class);

        $repository
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([
                $this->createUser(),
            ]);

        $handler = new GetUserHandler($db, $repository);

        $result = $handler->getAll();

        self::assertCount(1, $result);

        self::assertInstanceOf(
            UserDTO::class,
            $result[0],
        );
    }

    public function testGetIdReturnsUserDto():void {
        $db = $this->createMock(Connection::class);
        $repository = $this->createMock(UserRepository::class);

        $repository
            ->expects($this->once())
            ->method('getById')
            ->willReturn(
                $this->createUser(),
            );

        $handler = new GetUserHandler($db, $repository);

        $result = $handler->getId(
            new UserId(
                '550e8400-e29b-41d4-a716-446655440000',
            ),
        );

        self::assertInstanceOf(
            UserDTO::class,
            $result,
        );
    }

    public function testGetIdThrowsExceptionWhenUserDoesNotExist():void {
        $db = $this->createMock(Connection::class);
        $repository = $this->createMock(UserRepository::class);

        $repository
            ->expects($this->once())
            ->method('getById')
            ->willReturn(false);

        $handler = new GetUserHandler($db, $repository);

        $this->expectException(
            NotFoundException::class,
        );

        $this->expectExceptionMessage(
            'User saknas',
        );

        $handler->getId(
            new UserId(
                '550e8400-e29b-41d4-a716-446655440000',
            ),
        );
    }

    private function createUser():User {
        return new User(
            new UserId(
                '550e8400-e29b-41d4-a716-446655440000',
            ),
            'Test',
            'User',
            1,
            new DateTimeValue(
                '2026-01-01T10:00:00+00:00',
            ),
            null,
        );
    }
}
