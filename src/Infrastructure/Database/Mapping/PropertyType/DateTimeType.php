<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Mapping\PropertyType;

use DateTimeImmutable;
use InvalidArgumentException;

class DateTimeType
{
    static public function denormalize(?string $value): ?DateTimeImmutable
    {
        if($value === null) {
            return null;
        }

        if (!is_string($value)) {
            throw new InvalidArgumentException('Data expected to be an string, '.get_debug_type($value).' given.');
        }

        return new DateTimeImmutable($value);
    }

    static public function normalize(?DateTimeImmutable $value): ?string
    {
        if($value === null) {
            return null;
        }

        return $value->format('Y-m-d H:i:s');
    }
}