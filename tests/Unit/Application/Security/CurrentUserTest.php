<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Security;

use App\Application\Security\CurrentUser;
use App\Domain\ValueObjects\UserId;
use PHPUnit\Framework\TestCase;

final class CurrentUserTest extends TestCase {
    public function testReturnsUserId() : void {
        $user = new CurrentUser(
            new UserId('550e8400-e29b-41d4-a716-446655440000', ),
            [],
            [],
        );

        self::assertSame(
            '550e8400-e29b-41d4-a716-446655440000',
            $user->getUserId()->toString(),
        );
    }

    public function testReturnsRoles() : void {
        $user = new CurrentUser(
            new UserId('550e8400-e29b-41d4-a716-446655440000', ),
            [
                'administrator',
                'time-attester',
            ],
            [],
        );

        self::assertCount(2, $user->getRoles());
    }

    public function testReturnsPermissions() : void {
        $user = new CurrentUser(
            new UserId('550e8400-e29b-41d4-a716-446655440000', ),
            [],
            [
                'time.approve',
                'time.export',
            ],
        );

        self::assertCount(2, $user->getPermissions());
    }

    public function testHasRoleReturnsTrue() : void {
        $user = new CurrentUser(
            new UserId('550e8400-e29b-41d4-a716-446655440000', ),
            [
                'time-attester',
            ],
            [],
        );

        self::assertTrue($user->hasRole('time-attester'));
    }

    public function testHasRoleReturnsFalse() : void {
        $user = new CurrentUser(
            new UserId('550e8400-e29b-41d4-a716-446655440000', ),
            [],
            [],
        );

        self::assertFalse($user->hasRole('time-attester'));
    }

    public function testHasPermissionReturnsTrue() : void {
        $user = new CurrentUser(
            new UserId('550e8400-e29b-41d4-a716-446655440000', ),
            [],
            [
                'time.approve',
            ],
        );

        self::assertTrue($user->hasPermission('time.approve'));
    }

    public function testHasPermissionReturnsFalse() : void {
        $user = new CurrentUser(
            new UserId('550e8400-e29b-41d4-a716-446655440000', ),
            [],
            [],
        );

        self::assertFalse($user->hasPermission('time.approve'));
    }
}
