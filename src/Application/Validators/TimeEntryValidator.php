<?php

declare(strict_types=1);

namespace App\Application\Validators;

use DateTimeImmutable;
use Ramsey\Uuid\Uuid;

class TimeEntryValidator {
    /**
     * @param array<string, mixed> $data
     * @return array<string, string>
     */
    public static function validate(array $data) : array {
        $errors = [];

        if (isset($data['TimeEntryId'])) {
            self::validateId($data, $errors);
        }
        self::validateUserId($data, $errors);
        self::validateDate($data, $errors);
        self::validateDuration($data, $errors);
        self::validateActivityRole($data, $errors);
        self::validateGroupId($data, $errors);

        return $errors;
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $errors
     */
    private static function validateId(array $data, array &$errors) : void {
        if ($data['TimeEntryId'] !== $data['id']) {
            $errors['id'] = 'Id matchar inte';
        }
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $errors
     */
    private static function validateUserId(array $data, array &$errors) : void {
        if (!isset($data['userId']) || empty($data['userId'])) {
            $errors['userId'] = 'Användare är obligatorisk';
        } elseif (!Uuid::isValid($data['userId'])) {
            $errors['userId'] = 'AnvändareID är ogiltigt';
        }
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $errors
     */
    private static function validateDate(array $data, array &$errors) : void {
        if (!isset($data['date']) || empty($data['date'])) {
            $errors['date'] = 'Datum är obligatoriskt';

            return;
        }

        $date = DateTimeImmutable::createFromFormat(
            'Y-m-d',
            (string)$data['date'],
        );

        if ($date === false) {
            $errors['date'] = 'Datum måste anges som YYYY-MM-DD';

            return;
        }
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $errors
     */
    private static function validateDuration(array $data, array &$errors) : void {
        if (!isset($data['duration']) || empty($data['duration'])) {
            $errors['duration'] = 'Tid är obligatorisk';

            return;
        }

        $duration = (string)$data['duration'];

        $valid =
            DateTimeImmutable::createFromFormat('H:i', $duration) !== false
            || DateTimeImmutable::createFromFormat('H:i:s', $duration) !== false;

        if (!$valid) {
            $errors['duration'] = 'Tid måste anges som HH:MM eller HH:MM:SS';
        }
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $errors
     */
    private static function validateActivityRole(array $data, array &$errors) : void {
        if (
            !isset($data['activityRole']) ||
            empty(trim((string)$data['activityRole']))
        ) {
            $errors['activityRole'] =
                'Aktivitetsroll är obligatorisk';
        }
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $errors
     */
    private static function validateGroupId(array $data, array &$errors) : void {
        if (!isset($data['groupId']) || empty($data['groupId'])) {
            return;
        }

        if (!Uuid::isValid($data['groupId'])) {
            $errors['groupId'] = 'GruppId måste vara ett giltigt UUID';
        }
    }
}
