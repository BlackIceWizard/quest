<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Mapping\Quest;

use Closure;
use RiverRing\OwlOrm\Mapping\AbstractAggregateRootMapper;
use RiverRing\OwlOrm\Mapping\Extract;
use RiverRing\Quest\Domain\Quest\Media;
use RiverRing\Quest\Domain\Quest\Quest;
use RiverRing\Quest\Domain\Quest\QuestId;
use RiverRing\Quest\Infrastructure\Database\Mapping\Property\DateTimeType;

final class QuestMapper extends AbstractAggregateRootMapper
{
    public function applicableFor(): string
    {
        return Quest::class;
    }

    public function embeddable(): array
    {
        return [];
    }

    public function hydrationClosure(): Closure
    {
        return function (Extract $extract): void {
            /** @var Quest $this */

            $data = $extract->data();
            $entities = $extract->entities();

            $this->id = QuestId::fromString($data['id']);
            $this->name = $data['name'];
            $this->media = $entities[Media::class];
            $this->createdAt = DatetimeType::denormalize($data['created_at']);
        };
    }

    public function dehydrationClosure(): Closure
    {
        return function (): Extract {
            /** @var Quest $this */
            return Extract::ofAggregateRoot(
                [
                    'id' => $this->id->toString(),
                    'name' => $this->name,
                    'created_at' => DatetimeType::normalize($this->createdAt),
                ],
                [
                    Media::class => $this->media,
                ]
            );
        };
    }
}