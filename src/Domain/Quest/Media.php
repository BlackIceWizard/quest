<?php
declare(strict_types=1);

namespace RiverRing\Quest\Domain\Quest;

use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;
use RiverRing\Quest\Domain\File;

class Media
{
    private UuidInterface $id;
    private string $name;
    private File $file;
    private DateTimeImmutable $createdAt;

    public function __construct(UuidInterface $id, string $name, File $file)
    {
        $this->id = $id;
        $this->name = $name;
        $this->file = $file;
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

    public function file(): File
    {
        return $this->file;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}