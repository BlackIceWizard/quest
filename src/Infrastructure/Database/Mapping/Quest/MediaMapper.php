<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Mapping\Quest;

use Closure;
use JetBrains\PhpStorm\Pure;
use Ramsey\Uuid\Uuid;
use RiverRing\Quest\Domain\File;
use RiverRing\Quest\Domain\Quest\Media;
use RiverRing\Quest\Infrastructure\Database\Mapping\AbstractEntityMapper;
use RiverRing\Quest\Infrastructure\Database\Mapping\Extract;
use RiverRing\Quest\Infrastructure\Database\Mapping\Property\DateTimeType;
use RiverRing\Quest\Infrastructure\Database\Specification\EmbeddableSpecification;

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
        return function (Extract $extract): void {
            /** @var Media $this */

            $data = $extract->data();
            $embeddable = $extract->embeddable();

            $this->id = Uuid::fromString($data['id']);
            $this->name = $data['name'];
            $this->file = $embeddable[MediaMapper::EMBEDDABLE_FILE];
            $this->createdAt = DatetimeType::denormalize($data['created_at']);
        };
    }

    public function dehydrationClosure(): Closure
    {
        return function (): Extract {
            /** @var Media $this */

            return Extract::ofEntity(
                [
                    'id' => $this->id->toString(),
                    'name' => $this->name,
                    'created_at' => DatetimeType::normalize($this->createdAt),
                ],
                [
                    MediaMapper::EMBEDDABLE_FILE => $this->file,
                ]
            );
        };
    }
}