<?php
declare(strict_types=1);

namespace RiverRing\Quest\Domain;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

trait AggregateRootIdTrait
{
    private UuidInterface $id;

    private function __construct(UuidInterface $id)
    {
        $this->id = $id;
    }

    public function toString(): string
    {
        return $this->id->toString();
    }

    public static function generate(): self
    {
        return new self(Uuid::uuid4());
    }

    public static function fromString(string $id): self
    {
        return new self(Uuid::fromString($id));
    }
}