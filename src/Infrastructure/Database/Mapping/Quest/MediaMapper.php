<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Mapping\Quest;

use Closure;
use Ramsey\Uuid\Uuid;
use RiverRing\Quest\Domain\Quest\Media;
use RiverRing\Quest\Domain\Quest\Quest;
use RiverRing\Quest\Infrastructure\Database\Mapping\AbstractEntityMapper;
use RiverRing\Quest\Infrastructure\Database\Mapping\PropertyType\DateTimeType;

class MediaMapper extends AbstractEntityMapper
{
    /**
     * @inheritDoc
     */
    public function applicableFor(): string
    {
        return Media::class;
    }

    public function hydrationClosure(): Closure
    {
        return function (array $data): void
        {
            /** @var Media $this */
            $this->id = Uuid::fromString($data['id']);
            $this->name = $data['name'];
            
            $this->createdAt = DatetimeType::denormalize($data['created_at']);
        };
    }

    public function dehydrationClosure(): Closure
    {
        return function (): array
        {
            /** @var Quest $this */
            return [
                'id' => $this->id->toString(),
                'name' => $this->name,
                'created_at' => DatetimeType::normalize($this->createdAt),
            ];
        };
    }
}