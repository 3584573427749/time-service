<?php

declare(strict_types=1);

use App\Domain\ValueObjects\DateValue;
use PHPUnit\Framework\TestCase;

final class DateValueTest extends TestCase {
    public function testValidDate() : void {
        $date = new DateValue('2025-04-05');

        $this->assertSame('2025-04-05', (string)$date);
    }

    public function testInvalidDateFormat() : void {
        $this->expectException(InvalidArgumentException::class);

        new DateValue('05-04-2025');
    }

    public function testInvalidCalendarDate() : void {
        $this->expectException(InvalidArgumentException::class);

        new DateValue('2025-13-33');
    }

    public function testFromString() : void {
        $date = DateValue::fromString('2025-04-05');

        $this->assertSame(
            '2025-04-05',
            $date->toString(),
        );
    }

    public function testToDateTimeImmutable() : void {
        $date = new DateValue('2025-04-05');

        $this->assertInstanceOf(
            DateTimeImmutable::class,
            $date->toDateTimeImmutable(),
        );

        $this->assertSame(
            '2025-04-05',
            $date->toDateTimeImmutable()->format('Y-m-d'),
        );
    }

    public function testJsonSerialize() : void {
        $date = new DateValue('2025-04-05');

        $this->assertSame(
            '2025-04-05',
            $date->jsonSerialize(),
        );
    }

    public function testEqualsReturnsTrueWhenDatesAreEqual() : void {
        $first = new DateValue('2025-04-05');
        $second = new DateValue('2025-04-05');

        $this->assertTrue(
            $first->equals($second),
        );
    }

    public function testEqualsReturnsFalseWhenDatesDiffer() : void {
        $first = new DateValue('2025-04-05');
        $second = new DateValue('2025-04-06');

        $this->assertFalse(
            $first->equals($second),
        );
    }

    public function testIsAfterReturnsTrue() : void {
        $first = new DateValue('2025-04-06');
        $second = new DateValue('2025-04-05');

        $this->assertTrue(
            $first->isAfter($second),
        );
    }

    public function testIsAfterReturnsFalse() : void {
        $first = new DateValue('2025-04-05');
        $second = new DateValue('2025-04-06');

        $this->assertFalse(
            $first->isAfter($second),
        );
    }

    public function testIsFutureReturnsTrue() : void {
        $futureDate = new DateValue('2999-12-31');

        $this->assertTrue(
            $futureDate->isFuture(),
        );
    }

    public function testIsFutureReturnsFalse() : void {
        $today = new DateValue(date('Y-m-d'));

        $this->assertFalse(
            $today->isFuture(),
        );
    }
}
