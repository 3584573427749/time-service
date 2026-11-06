<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers\User;

use App\Application\Handlers\User\DeleteUserHandler;
use App\Domain\Repositories\UserRepository;
use App\Domain\ValueObjects\UserId;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;

final class DeleteUserHandlerTest extends TestCase {
    public function testHandleDeletesUser() : void {
        $db = $this->createMock(Connection::class);
        $repository = $this->createMock(UserRepository::class);

        $repository
            ->expects($this->once())
            ->method('delete')
            ->with(
                $this->isInstanceOf(UserId::class),
            );

        $handler = new DeleteUserHandler($db, $repository);

        $handler->handle(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
        );
    }
}
