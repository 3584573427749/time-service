<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use InvalidArgumentException;
use JsonSerializable;

class TimeValue implements JsonSerializable {
    private string $value;

    public function __construct(string $value) {
        if (!preg_match(
            '/^(\d{1,3}):(\d{2})(:\d{2})?$/',
            $value,
            $matches,
        )) {
            throw new InvalidArgumentException(
                sprintf('Invalid time value: %s', $value),
            );
        }

        $hours = (int)$matches[1];
        $minutes = (int)$matches[2];
        $seconds = isset($matches[3])
            ? (int)substr($matches[3], 1)
            : 0;

        if ($minutes > 59) {
            throw new InvalidArgumentException(sprintf('Invalid time value: %s', $value), );
        }

        $this->value = sprintf(
            '%02d:%02d:%02d',
            $hours,
            $minutes,
            $seconds,
        );
    }

    public static function fromSeconds(int $seconds) : self {
        $hours = intdiv($seconds, 3600);
        $seconds -= $hours * 3600;
        $minutes = intdiv($seconds, 60);
        $seconds -= $minutes * 60;

        return new self(
            sprintf(
                '%02d:%02d:%02d',
                $hours,
                $minutes,
                $seconds,
            ),
        );
    }

    public function toString() : string {
        return $this->value;
    }

    public function __toString() : string {
        return $this->toString();
    }

    public function toSeconds() : int {
        [$hours, $minutes, $seconds] = explode(':', $this->value);

        return ((int)$hours * 3600)
            + ((int)$minutes * 60)
            + (int)$seconds;
    }

    public function jsonSerialize() : string {
        return $this->toString();
    }

    public function equals(self $other) : bool {
        return $this->value === $other->value;
    }
}
