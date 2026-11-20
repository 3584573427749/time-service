<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Validators;

use App\Application\Validators\SearchTimeEntriesValidator;
use PHPUnit\Framework\TestCase;

final class SearchTimeEntriesValidatorTest extends TestCase {
    public function testValidationPassesWithEmptyRequest() : void {
        self::assertSame([], SearchTimeEntriesValidator::validate([]));
    }

    public function testValidationFailsWhenUsersIsNotArray() : void {
        $errors = SearchTimeEntriesValidator::validate(['users' => 'invalid', ]);

        self::assertSame(['users' => 'Users måste vara en array', ], $errors);
    }

    public function testValidationFailsWhenUsersArrayIsEmpty() : void {
        $errors = SearchTimeEntriesValidator::validate(['users' => [], ]);

        self::assertSame(['users' => 'Users-listan får inte vara tom', ], $errors);
    }

    public function testValidationFailsWhenUserIdIsInvalid() : void {
        $errors = SearchTimeEntriesValidator::validate(['users' => ['invalid', ], ]);

        self::assertSame(['users.0' => 'AnvändareID måste vara ett giltigt UUID', ], $errors);
    }

    public function testValidationFailsWhenFromDateIsInvalid() : void {
        $errors = SearchTimeEntriesValidator::validate(['from' => 'invalid', ]);

        self::assertSame(['from' => 'From måste anges som YYYY-MM-DD', ], $errors);
    }

    public function testValidationFailsWhenToDateIsInvalid() : void {
        $errors = SearchTimeEntriesValidator::validate(['to' => 'invalid', ]);

        self::assertSame(['to' => 'To måste anges som YYYY-MM-DD', ], $errors);
    }

    public function testValidationFailsWhenStatusIsInvalid() : void {
        $errors = SearchTimeEntriesValidator::validate(['status' => 'invalid', ]);

        self::assertSame(['status' => 'Ogiltig status', ], $errors);
    }

    public function testValidationFailsWhenActivityRoleIsEmpty() : void {
        $errors = SearchTimeEntriesValidator::validate(['activityRole' => '   ', ]);

        self::assertSame(['activityRole' => 'Aktivitetsroll får inte vara tom', ], $errors);
    }

    public function testValidationFailsWhenFirstRecordIsNegative() : void {
        $errors = SearchTimeEntriesValidator::validate(['firstRecord' => -1, ]);

        self::assertSame(['firstRecord' => 'FirstRecord måste vara 0 eller större', ], $errors);
    }

    public function testValidationFailsWhenMaxRecordsIsZero() : void {
        $errors = SearchTimeEntriesValidator::validate(['maxRecords' => 0, ]);

        self::assertSame(['maxRecords' => 'MaxRecords måste vara större än 0', ], $errors);
    }

    public function testValidationFailsWhenSortByIsInvalid() : void {
        $errors = SearchTimeEntriesValidator::validate([
            'sortBy' => 'invalid',
        ]);

        self::assertSame(
            [
                'sortBy' => 'Ogiltigt sorteringsfält',
            ],
            $errors,
        );
    }

    public function testValidationFailsWhenSortDirectionIsInvalid() : void {
        $errors = SearchTimeEntriesValidator::validate([
            'sortDirection' => 'invalid',
        ]);

        self::assertSame(['sortDirection' => 'SortDirection måste vara asc eller desc', ], $errors);
    }

    public function testValidationPassesWithValidData() : void {
        $errors = SearchTimeEntriesValidator::validate([
            'users' => [
                '550e8400-e29b-41d4-a716-446655440000',
            ],
            'from' => '2026-11-01',
            'to' => '2026-11-30',
            'status' => 'approved',
            'activityRole' => 'Ledare',
            'firstRecord' => 0,
            'maxRecords' => 25,
            'sortBy' => 'date',
            'sortDirection' => 'asc',
        ]);

        self::assertSame([], $errors);
    }
}
