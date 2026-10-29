<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\ValueObjects;

use App\Domain\ValueObjects\DateTimeValue;
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class DateTimeValueTest extends TestCase {
    public function testIsoStringIsAccepted() : void {
        $dt = new DateTimeValue('2025-04-05T12:00:00Z');

        $this->assertSame('2025-04-05 12:00:00', $dt->toString());
    }

    public function testTimezoneIsNormalizedToUtc() : void {
        $dt = new DateTimeValue('2025-04-05T14:00:00+02:00');

        $this->assertSame('2025-04-05 12:00:00', $dt->toString());
    }

    public function testDateTimeInterfaceIsAccepted() : void {
        $input = new DateTimeImmutable(
            '2025-04-05 14:00:00',
            new DateTimeZone('Europe/Stockholm'),
        );

        $dt = new DateTimeValue($input);

        $this->assertSame(
            '2025-04-05 15:00:00',
            $dt->toString(),
        );
    }

    public function testEqualsWithSameInstant() : void {
        $a = new DateTimeValue('2025-04-05T12:00:00Z');
        $b = new DateTimeValue('2025-04-05T14:00:00+02:00');

        $this->assertTrue($a->equals($b));
    }

    public function testEqualsWithDifferentInstant() : void {
        $a = new DateTimeValue('2025-04-05T12:00:00Z');
        $b = new DateTimeValue('2025-04-05T12:01:00Z');

        $this->assertFalse($a->equals($b));
    }

    public function testInvalidDateTimeThrowsException() : void {
        $this->expectException(InvalidArgumentException::class);

        new DateTimeValue('not-a-datetime');
    }

    public function testJsonSerialization() : void {
        $dt = new DateTimeValue('2025-04-05T12:00:00Z');

        $json = json_encode(['time' => $dt], JSON_THROW_ON_ERROR);

        $this->assertSame(
            '{"time":"2025-04-05 12:00:00"}',
            $json,
        );
    }

    public function testFromStringCreatesDateTimeValue() : void {
        $value = DateTimeValue::fromString('2026-10-29T15:30:45+02:00');

        self::assertSame(
            '2026-10-29T13:30:45+00:00',
            $value->toISOString(),
        );
    }

    public function testToDateTimeImmutableReturnsDateTimeImmutable() : void {
        $value = DateTimeValue::fromString('2026-10-29T15:30:45+02:00');

        self::assertInstanceOf(
            DateTimeImmutable::class,
            $value->toDateTimeImmutable(),
        );
    }

    public function testToDateTimeImmutableContainsCorrectValue() : void {
        $value = DateTimeValue::fromString('2026-10-29T15:30:45+02:00');

        self::assertSame(
            '2026-10-29T13:30:45+00:00',
            $value->toDateTimeImmutable()->format(DATE_ATOM),
        );
    }

    public function testToISOStringReturnsAtomFormattedDate() : void {
        $value = DateTimeValue::fromString('2026-10-29T15:30:45+02:00');

        self::assertSame(
            '2026-10-29T13:30:45+00:00',
            $value->toISOString(),
        );
    }

    public function testCanBeCastToString() : void {
        $value = DateTimeValue::fromString('2026-10-29T15:30:45+02:00');

        self::assertSame(
            $value->toString(),
            (string) $value,
        );
    }
}
