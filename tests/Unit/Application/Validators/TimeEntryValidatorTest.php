<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Validators;

use App\Application\Validators\TimeEntryValidator;
use PHPUnit\Framework\TestCase;

final class TimeEntryValidatorTest extends TestCase {
    public function testReturnsNoErrorsForValidInput() : void {
        $errors = TimeEntryValidator::validate($this->getValidData());
        self::assertSame([], $errors);

        $errors = TimeEntryValidator::validate($this->getValidData([
            'id' => null, 'TimeEntryId' => null,
        ]));
        self::assertSame([], $errors);

    }

    public function testReturnsErrorWhenIdsMismatch() : void {
        $errors = TimeEntryValidator::validate($this->getValidData([
            'id' => '12345678-e29b-41d4-a716-446655440001',
        ]));

        self::assertArrayHasKey('id', $errors);
    }

    public function testReturnsErrorWhenUserIdIsMissing() : void {
        $errors = TimeEntryValidator::validate($this->getValidData([
            'userId' => null,
        ]));

        self::assertArrayHasKey('userId', $errors);
    }

    public function testReturnsErrorWhenUserIdIsInvalid() : void {
        $errors = TimeEntryValidator::validate($this->getValidData([
            'userId' => 'invalid',
        ]));

        self::assertSame(
            'AnvändareID är ogiltigt',
            $errors['userId'],
        );
    }

    public function testReturnsErrorWhenDateIsMissing() : void {
        $errors = TimeEntryValidator::validate($this->getValidData([
            'date' => null,
        ]));

        self::assertArrayHasKey('date', $errors);
    }

    public function testReturnsErrorWhenDateFormatIsInvalid() : void {
        $errors = TimeEntryValidator::validate($this->getValidData([
            'date' => '05/11/2026',
        ]));

        self::assertSame(
            'Datum måste anges som YYYY-MM-DD',
            $errors['date'],
        );
    }

    public function testReturnsErrorWhenDurationIsMissing() : void {
        $errors = TimeEntryValidator::validate($this->getValidData([
            'duration' => null,
        ]));

        self::assertArrayHasKey('duration', $errors);
    }

    public function testAcceptsHourAndMinuteDuration() : void {
        $errors = TimeEntryValidator::validate($this->getValidData([
            'duration' => '03:05',
        ]));

        self::assertArrayNotHasKey('duration', $errors);
    }

    public function testAcceptsHourMinuteAndSecondDuration() : void {
        $errors = TimeEntryValidator::validate($this->getValidData([
            'duration' => '03:05:00',
        ]));

        self::assertArrayNotHasKey('duration', $errors);
    }

    public function testReturnsErrorForInvalidDuration() : void {
        $errors = TimeEntryValidator::validate($this->getValidData([
            'duration' => 'abc',
        ]));

        self::assertSame(
            'Tid måste anges som HH:MM eller HH:MM:SS',
            $errors['duration'],
        );
    }

    public function testReturnsErrorWhenActivityRoleIsMissing() : void {
        $errors = TimeEntryValidator::validate($this->getValidData([
            'activityRole' => '',
        ]));

        self::assertSame(
            'Aktivitetsroll är obligatorisk',
            $errors['activityRole'],
        );
    }

    public function testReturnsErrorWhenGroupIdIsInvalid() : void {
        $errors = TimeEntryValidator::validate($this->getValidData([
            'groupId' => 'invalid',
        ]));

        self::assertSame(
            'GruppId måste vara ett giltigt UUID',
            $errors['groupId'],
        );
    }

    public function testAcceptsMissingGroupId() : void {
        $data = $this->getValidData();
        unset($data['groupId']);

        $errors = TimeEntryValidator::validate($data);

        self::assertArrayNotHasKey('groupId', $errors);
    }

    /**
     * @param array<string, mixed> $override
     * @return array<string, mixed>
     */
    private function getValidData(array $override = []) : array {
        return array_merge([
            'TimeEntryId' => '12345678-e29b-41d4-a716-446655440000',
            'id' => '12345678-e29b-41d4-a716-446655440000',
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'date' => '2026-11-05',
            'duration' => '03:05',
            'activityRole' => 'Ledare',
            'groupId' => '660e8400-e29b-41d4-a716-446655440000',
            'comment' => 'Test',
        ], $override);
    }
}
