<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Domain\Entities\User;
use App\Domain\Exception\NotFoundException;
use App\Domain\Repositories\UserRepository;
use App\Domain\ValueObjects\UserId;
use Doctrine\DBAL\Exception;

class DbalUserRepository extends AbstractDbRepository implements UserRepository {
    private const TABLE = 'users';

    /**
     * @inheritDoc
     */
    public function getAll() : array {
        $rows = $this->connection->executeQuery('SELECT * FROM ' . self::TABLE)
                                 ->fetchAllAssociative();

        return array_map(fn ($row) => User::fromDBRow($row), $rows);
    }

    public function getById(UserId $id) : User|false {
        $row = $this->connection->executeQuery('SELECT * FROM ' . self::TABLE . ' WHERE id = ? ', [$id->toString()])
                                ->fetchAssociative();

        if ($row === false) {
            return false;
        }

        return User::fromDBRow($row);
    }

    /**
     * @throws Exception
     */
    public function delete(UserId $id) : void {
        $affectedRows = $this->connection->delete(self::TABLE, ['id' => $id->toString()]);

        if ($affectedRows === 0) {
            throw new NotFoundException('User saknas');
        }
    }

    public function create(User $user) : void {
        $this->connection->insert(self::TABLE, $user->asDBRow());
    }

    public function update(User $user) : void {
        $this->connection->update(self::TABLE, $user->asDBRow(), ['id' => $user->getId()->toString()]);
    }
}
