<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Mapping\Quest;

use Closure;
use RiverRing\Quest\Domain\Quest\Media;
use RiverRing\Quest\Domain\Quest\Quest;
use RiverRing\Quest\Domain\Quest\QuestId;
use RiverRing\Quest\Infrastructure\Database\Mapping\AbstractAggregateRootMapper;
use RiverRing\Quest\Infrastructure\Database\Mapping\PropertyType\DateTimeType;

class QuestMapper extends AbstractAggregateRootMapper
{
    /**
     * @inheritDoc
     */
    public function applicableFor(): string
    {
        return Quest::class;
    }

    public function hydrationClosure(): Closure
    {
        return function (array $data, array $entities): void
        {
            /** @var Quest $this */
            $this->id = QuestId::fromString($data['id']);
            $this->name = $data['name'];
            $this->media = $entities[Media::class];
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