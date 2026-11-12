<?php

declare(strict_types=1);

namespace App\Application\Security;

use App\Domain\ValueObjects\UserId;

class CurrentUser {
    /**
     * @param array<string> $roles
     * @param array<string> $permissions
     */
    public function __construct(
        private UserId $userId,
        private array $roles,
        private array $permissions,
    ) {
    }

    public function getUserId() : UserId {
        return $this->userId;
    }

    /**
     * @return array<string>
     */
    public function getRoles() : array {
        return $this->roles;
    }

    /**
     * @return array<string>
     */
    public function getPermissions() : array {
        return $this->permissions;
    }

    public function hasRole(string $role) : bool {
        return in_array(
            $role,
            $this->roles,
            true,
        );
    }

    public function hasPermission(string $permission) : bool {
        return in_array(
            $permission,
            $this->permissions,
            true,
        );
    }
}
