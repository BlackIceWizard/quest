<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Mapping\Quest;

use Closure;
use JetBrains\PhpStorm\Pure;
use Ramsey\Uuid\Uuid;
use RiverRing\Quest\Domain\File;
use RiverRing\Quest\Domain\Quest\Media;
use RiverRing\Quest\Domain\Quest\Quest;
use RiverRing\Quest\Infrastructure\Database\Mapping\AbstractEntityMapper;
use RiverRing\Quest\Infrastructure\Database\Mapping\Embeddable\EmbeddableSpecification;
use RiverRing\Quest\Infrastructure\Database\Mapping\Property\DateTimeType;

final class MediaMapper extends AbstractEntityMapper
{
    public const EMBEDDABLE_FILE = 'file';

    public function applicableFor(): string
    {
        return Media::class;
    }

    #[Pure]
    public function embeddable(): array
    {
        return [
            self::EMBEDDABLE_FILE => new EmbeddableSpecification(File::class, 'file_')
        ];
    }

    public function hydrationClosure(): Closure
    {
        return function (array $data, array $embeddable): void
        {
            /** @var Media $this */
            $this->id = Uuid::fromString($data['id']);
            $this->name = $data['name'];
            $this->file = $embeddable[MediaMapper::EMBEDDABLE_FILE];
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