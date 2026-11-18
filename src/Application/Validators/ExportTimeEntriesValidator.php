<?php

declare(strict_types=1);

namespace App\Application\Validators;

use Ramsey\Uuid\Uuid;

class ExportTimeEntriesValidator {
    /**
     * @param array<string, mixed> $data
     * @return array<string, string>
     */
    public static function validate(array $data) : array {
        $errors = [];

        if (isset($data['users'])) {
            self::validateUsers($data, $errors);
        }

        return $errors;
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $errors
     */
    private static function validateUsers(
        array $data,
        array &$errors,
    ) : void {
        if (!is_array($data['users'])) {
            $errors['users'] = 'Users måste vara en array';

            return;
        }

        if (count($data['users']) === 0) {
            $errors['users'] =
                'Users-listan får inte vara tom';

            return;
        }

        foreach ($data['users'] as $index => $userId) {
            if (!Uuid::isValid((string)$userId)) {
                $errors["users.$index"] = 'AnvändareID måste vara ett giltigt UUID';
            }
        }
    }
}
