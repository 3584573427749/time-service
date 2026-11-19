<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Validators;

use App\Application\Validators\ExportTimeEntriesValidator;
use PHPUnit\Framework\TestCase;

final class ExportTimeEntriesValidatorTest extends TestCase {
    public function testValidationPassesWhenUsersIsMissing() : void {
        $errors = ExportTimeEntriesValidator::validate([]);

        self::assertSame([], $errors);
    }

    public function testValidationFailsWhenUsersIsNotArray() : void {
        $errors = ExportTimeEntriesValidator::validate([
            'users' => 'invalid',
        ]);

        self::assertSame(
            [
                'users' => 'Users måste vara en array',
            ],
            $errors,
        );
    }

    public function testValidationFailsWhenUsersArrayIsEmpty() : void {
        $errors = ExportTimeEntriesValidator::validate([
            'users' => [],
        ]);

        self::assertSame(
            [
                'users' => 'Users-listan får inte vara tom',
            ],
            $errors,
        );
    }

    public function testValidationPassesWhenAllUserIdsAreValid() : void {
        $errors = ExportTimeEntriesValidator::validate([
            'users' => [
                '550e8400-e29b-41d4-a716-446655440000',
                '550e8400-e29b-41d4-a716-446655440001',
            ],
        ]);

        self::assertSame([], $errors);
    }

    public function testValidationFailsWhenUserIdIsInvalid() : void {
        $errors = ExportTimeEntriesValidator::validate([
            'users' => [
                '550e8400-e29b-41d4-a716-446655440000',
                'invalid',
            ],
        ]);

        self::assertSame(
            [
                'users.1' => 'AnvändareID måste vara ett giltigt UUID',
            ],
            $errors,
        );
    }

    public function testValidationFailsWhenSeveralUserIdsAreInvalid() : void {
        $errors = ExportTimeEntriesValidator::validate([
            'users' => [
                'invalid-1',
                '550e8400-e29b-41d4-a716-446655440000',
                'invalid-2',
            ],
        ]);

        self::assertSame(
            [
                'users.0' => 'AnvändareID måste vara ett giltigt UUID',
                'users.2' => 'AnvändareID måste vara ett giltigt UUID',
            ],
            $errors,
        );
    }
}
