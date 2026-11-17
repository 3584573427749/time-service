<?php

declare(strict_types=1);

namespace App\Application\Security;

use App\Domain\Enums\Permission;
use App\Domain\ValueObjects\UserId;

class CurrentUser {
    /**
     * @var array<Permission>
     */
    private array $permissions;

    /**
     * @param array<string> $roles
     * @param array<string> $permissions
     */
    public function __construct(
        private UserId $userId,
        private array $roles,
        array $permissions,
    ) {
        $this->permissions = array_map(
            static fn (string $permission) : ?Permission => Permission::tryFrom($permission),
            $permissions,
        )
                |> (fn ($x) => array_filter($x, static fn (?Permission $permission) : bool => $permission !== null, ))
                |> array_values(...);
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
     * @return array<Permission>
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

    public function hasPermission(Permission $permission) : bool {
        return in_array(
            $permission,
            $this->permissions,
            true,
        );
    }
}
