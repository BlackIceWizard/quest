<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Mapping\Embeddable;

use Closure;
use RiverRing\Quest\Domain\File;
use RiverRing\Quest\Infrastructure\Database\Mapping\AbstractEmbeddedMapper;
use RiverRing\Quest\Infrastructure\Database\Mapping\Extract;

final class FileMapper extends AbstractEmbeddedMapper
{
    public function applicableFor(): string
    {
        return File::class;
    }

    public function hydrationClosure(): Closure
    {
        return function (Extract $extract): void
        {
            /** @var File $this */

            $data = $extract->data();

            $this->mimeType = $data['mime_type'];
            $this->size = $data['size'];
            $this->location = $data['location'];
        };
    }

    public function dehydrationClosure(): Closure
    {
        return function (): Extract {
            /** @var File $this */

            return Extract::ofEmbeddable(
                [
                    'mime_type' => $this->mimeType,
                    'size' => $this->size,
                    'location' => $this->location,
                ]
            );
        };
    }
}