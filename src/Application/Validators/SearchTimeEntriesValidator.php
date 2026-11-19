<?php

declare(strict_types=1);

namespace App\Application\Validators;

use App\Domain\Enums\TimeEntryStatus;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;

class SearchTimeEntriesValidator {
    /**
     * @param array<string,mixed> $data
     * @return array<string,string>
     */
    public static function validate(array $data) : array {
        $errors = [];

        self::validateUsers($data, $errors);
        self::validateFromDate($data, $errors);
        self::validateToDate($data, $errors);
        self::validateStatus($data, $errors);
        self::validateActivityRole($data, $errors);
        self::validateFirstRecord($data, $errors);
        self::validateMaxRecords($data, $errors);
        self::validateSortBy($data, $errors);
        self::validateSortDirection($data, $errors);

        return $errors;
    }

    /**
     * @param array<string,mixed> $data
     * @param array<string,string> $errors
     */
    private static function validateUsers(array $data, array &$errors) : void {
        if (!isset($data['users'])) {
            return;
        }

        if (!is_array($data['users'])) {
            $errors['users'] = 'Users måste vara en array';

            return;
        }

        if (count($data['users']) === 0) {
            $errors['users'] = 'Users-listan får inte vara tom';

            return;
        }

        foreach ($data['users'] as $index => $userId) {
            if (!Uuid::isValid((string)$userId)) {
                $errors["users.$index"] = 'AnvändareID måste vara ett giltigt UUID';
            }
        }
    }

    /**
     * @param array<string,mixed> $data
     * @param array<string,string> $errors
     */
    private static function validateFromDate(array $data, array &$errors) : void {
        if (!isset($data['from'])) {
            return;
        }

        if (DateTimeImmutable::createFromFormat('Y-m-d', (string)$data['from']) === false) {
            $errors['from'] = 'From måste anges som YYYY-MM-DD';
        }
    }

    /**
     * @param array<string,mixed> $data
     * @param array<string,string> $errors
     */
    private static function validateToDate(array $data, array &$errors) : void {
        if (!isset($data['to'])) {
            return;
        }

        if (DateTimeImmutable::createFromFormat('Y-m-d', (string)$data['to']) === false) {
            $errors['to'] = 'To måste anges som YYYY-MM-DD';
        }
    }

    /**
     * @param array<string,mixed> $data
     * @param array<string,string> $errors
     */
    private static function validateStatus(array $data, array &$errors) : void {
        if (!isset($data['status'])) {
            return;
        }

        if (TimeEntryStatus::tryFrom((string)$data['status']) === null) {
            $errors['status'] = 'Ogiltig status';
        }
    }

    /**
     * @param array<string,mixed> $data
     * @param array<string,string> $errors
     */
    private static function validateActivityRole(array $data, array &$errors) : void {
        if (!isset($data['activityRole'])) {
            return;
        }

        if (trim((string)$data['activityRole']) === '') {
            $errors['activityRole'] = 'Aktivitetsroll får inte vara tom';
        }
    }

    /**
     * @param array<string,mixed> $data
     * @param array<string,string> $errors
     */
    private static function validateFirstRecord(array $data, array &$errors) : void {
        if (!isset($data['firstRecord'])) {
            return;
        }

        $value = filter_var($data['firstRecord'], FILTER_VALIDATE_INT);

        if ($value === false || $value < 0) {
            $errors['firstRecord'] = 'FirstRecord måste vara 0 eller större';
        }
    }

    /**
     * @param array<string,mixed> $data
     * @param array<string,string> $errors
     */
    private static function validateMaxRecords(array $data, array &$errors) : void {
        if (!isset($data['maxRecords'])) {
            return;
        }

        $value = filter_var($data['maxRecords'], FILTER_VALIDATE_INT);

        if ($value === false || $value < 1) {
            $errors['maxRecords'] = 'MaxRecords måste vara större än 0';
        }
    }

    /**
     * @param array<string,mixed> $data
     * @param array<string,string> $errors
     */
    private static function validateSortBy(array $data, array &$errors) : void {
        if (!isset($data['sortBy'])) {
            return;
        }

        if (!in_array(
            $data['sortBy'],
            [
                'date',
                'user',
                'status',
            ],
            true,
        )
        ) {
            $errors['sortBy'] = 'Ogiltigt sorteringsfält';
        }
    }

    /**
     * @param array<string,mixed> $data
     * @param array<string,string> $errors
     */
    private static function validateSortDirection(array $data, array &$errors) : void {
        if (!isset($data['sortDirection'])) {
            return;
        }

        if (!in_array(
            strtolower((string)$data['sortDirection']),
            [
                'asc',
                'desc',
            ],
            true,
        )
        ) {
            $errors['sortDirection'] = 'SortDirection måste vara asc eller desc';
        }
    }
}
