<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\ValueObjects;

use App\Domain\ValueObjects\TimeValue;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class TimeValueTest extends TestCase {
    public function testCanBeCreatedFromHoursAndMinutes() : void {
        $value = new TimeValue('03:05');

        self::assertSame(
            '03:05:00',
            $value->toString(),
        );
    }

    public function testCanBeCreatedFromHoursMinutesAndSeconds() : void {
        $value = new TimeValue('03:05:17');

        self::assertSame(
            '03:05:17',
            $value->toString(),
        );
    }

    public function testCanBeCastToString() : void {
        $value = new TimeValue('03:05');

        self::assertSame(
            '03:05:00',
            (string)$value,
        );
    }

    public function testCanBeSerializedToJson() : void {
        $value = new TimeValue('03:05');

        self::assertSame(
            '03:05:00',
            $value->jsonSerialize(),
        );
    }

    public function testEqualsReturnsTrueForEqualValues() : void {
        $first = new TimeValue('03:05');
        $second = new TimeValue('03:05:00');

        self::assertTrue(
            $first->equals($second),
        );
    }

    public function testEqualsReturnsFalseForDifferentValues() : void {
        $first = new TimeValue('03:05');
        $second = new TimeValue('04:05');

        self::assertFalse(
            $first->equals($second),
        );
    }

    public function testCanConvertToSeconds() : void {
        $value = new TimeValue('03:05:00');

        self::assertSame(
            11100,
            $value->toSeconds(),
        );
    }

    public function testCanBeCreatedFromSeconds() : void {
        $value = TimeValue::fromSeconds(11100);

        self::assertSame(
            '03:05:00',
            $value->toString(),
        );
    }

    public function testCanRoundTripViaSeconds() : void {
        $original = new TimeValue('03:05:17');

        $copy = TimeValue::fromSeconds(
            $original->toSeconds(),
        );

        self::assertTrue(
            $original->equals($copy),
        );
    }

    public function testThrowsExceptionForInvalidFormat() : void {
        $this->expectException(
            InvalidArgumentException::class,
        );

        new TimeValue('abc');
    }

    public function testThrowsExceptionWhenMinutesAreGreaterThan59() : void {
        $this->expectException(
            InvalidArgumentException::class,
        );

        new TimeValue('03:99');
    }
}
