<?php
declare(strict_types=1);

namespace RiverRing\Quest\Domain\Quest;

use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;

class Quest
{
    private UuidInterface $id;
    private string $name;
    private DateTimeImmutable $createdAt;

    public function __construct(UuidInterface $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
        $this->createdAt = new DateTimeImmutable();
    }

    public function id(): UuidInterface
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}