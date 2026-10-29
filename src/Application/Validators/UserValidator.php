<?php

declare(strict_types=1);

namespace App\Application\Validators;

class UserValidator {
    /**
     * @param array<string, mixed> $data
     * @return array<string, string>
     */
    public static function validate(array $data) : array {
        $errors = [];

        self::validateId($data, $errors);
        self::validateFirstName($data, $errors);
        self::validateLastName($data, $errors);
        self::validateActive($data, $errors);

        return $errors;
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $errors
     */
    private static function validateId(array $data, array &$errors) : void {
        if (!isset($data['id'])) {
            $errors['id'] = 'Id saknas i anrop';
        }
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $errors
     */
    private static function validateFirstName(array $data, array &$errors) : void {
        if (!isset($data['firstName']) || empty($data['firstName'])) {
            $errors['firstName'] = 'Firstname är obligatoriskt';
        }
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $errors
     */
    private static function validateLastName(array $data, array &$errors) : void {
        if (!isset($data['lastName']) || empty($data['lastName'])) {
            $errors['lastName'] = 'Lastname är obligatoriskt';
        }
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $errors
     */
    private static function validateActive(array $data, array &$errors) : void {
        if (!isset($data['active'])) {
            $errors['active'] = 'Aktiv är obligatoriskt';
        } else {
            $active = filter_var($data['active'], FILTER_VALIDATE_INT);
            if ($active === false || ($active > 1 || $active < 0)) {
                $errors['active'] = 'Aktiv måste vara 1 eller 0';
            }
        }
    }
}
