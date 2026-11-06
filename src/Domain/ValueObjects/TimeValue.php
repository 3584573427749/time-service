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

        if ($minutes > 59) {
            throw new InvalidArgumentException(
                sprintf('Invalid time value: %s', $value),
            );
        }

        $this->value = sprintf('%s:%s', $hours, $minutes);
    }

    public static function fromString(string $value) : self {
        return new self($value);
    }

    public function toString() : string {
        return $this->value;
    }

    public function __toString() : string {
        return $this->toString();
    }

    public function jsonSerialize() : string {
        return $this->toString();
    }

    public function equals(self $other) : bool {
        return $this->value === $other->value;
    }
}
