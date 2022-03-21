<?php
declare(strict_types=1);

namespace RiverRing\Quest\Domain\Quest;

use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;

class Quest
{
    private QuestId $id;

    private string $name;

    /** @var Media[] */
    private array $media;

    private DateTimeImmutable $createdAt;

    public function __construct(QuestId $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
        $this->createdAt = new DateTimeImmutable();
    }

    public function addMedia(Media... $media) {
        array_push($this->media, ...$media);
    }

    public function id(): QuestId
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