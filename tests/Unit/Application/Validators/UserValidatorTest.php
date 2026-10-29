<?php

declare(strict_types=1);

namespace Application\Validators;

use App\Application\Validators\UserValidator;
use PHPUnit\Framework\TestCase;

final class UserValidatorTest extends TestCase {
    public function testValidateReturnsNoErrorsForValidData() : void {
        $data = [
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'firstName' => 'Anna',
            'lastName' => 'Andersson',
            'active' => 1,
        ];

        $errors = UserValidator::validate($data);

        self::assertSame([], $errors);
    }

    public function testValidateFailsWhenIdIsMissing() : void {
        $data = [
            'firstName' => 'Anna',
            'lastName' => 'Andersson',
            'active' => 1,
        ];

        $errors = UserValidator::validate($data);

        self::assertSame(
            'Id saknas i anrop',
            $errors['id'],
        );
    }

    public function testFirstNameIsRequired() : void {
        $data = [
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'lastName' => 'Andersson',
            'active' => 1,
        ];

        $errors = UserValidator::validate($data);

        self::assertSame(
            'Firstname är obligatoriskt',
            $errors['firstName'],
        );
    }

    public function testLastNameIsRequired() : void {
        $data = [
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'firstName' => 'Anna',
            'active' => 1,
        ];

        $errors = UserValidator::validate($data);

        self::assertSame(
            'Lastname är obligatoriskt',
            $errors['lastName'],
        );
    }

    public function testActiveIsRequired() : void {
        $data = [
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'firstName' => 'Anna',
            'lastName' => 'Andersson',
        ];
        $errors = UserValidator::validate($data);

        self::assertSame(
            'Aktiv är obligatoriskt',
            $errors['active'],
        );
    }

    public function testActiveMustBeZeroOrOne() : void {
        $data = [
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'firstName' => 'Anna',
            'lastName' => 'Andersson',
            'active' => -1,
        ];

        $errors = UserValidator::validate($data);

        self::assertSame(
            'Aktiv måste vara 1 eller 0',
            $errors['active'],
        );

        $data['active'] = 2;
        $errors = UserValidator::validate($data);

        self::assertSame(
            'Aktiv måste vara 1 eller 0',
            $errors['active'],
        );
    }

    public function testReturnsMultipleErrors() : void {
        $errors = UserValidator::validate([]);

        self::assertCount(4, $errors);

        self::assertArrayHasKey('id', $errors);
        self::assertArrayHasKey('firstName', $errors);
        self::assertArrayHasKey('lastName', $errors);
        self::assertArrayHasKey('active', $errors);
    }
}
