<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Mapping\Embeddable;

use Closure;
use RiverRing\Quest\Domain\File;
use RiverRing\Quest\Infrastructure\Database\Mapping\AbstractEmbeddableMapper;

final class FileMapper extends AbstractEmbeddableMapper
{
    public function applicableFor(): string
    {
        return File::class;
    }

    public function hydrationClosure(): Closure
    {
        return function (array $data, string $prefix): void
        {
            /** @var File $this */
            $this->mimeType = $data[$prefix.'mime_type'];
            $this->size = $data[$prefix.'size'];
            $this->location = $data[$prefix.'location'];
        };
    }

    public function dehydrationClosure(): Closure
    {
        // TODO: Implement dehydrationClosure() method.
    }
}