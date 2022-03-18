<?php
declare(strict_types=1);

namespace RiverRing\Quest\Domain;

class File
{
    private string $mimeType;
    private int $size;
    private string $location;

    public function __construct(string $mimeType, int $size, string $location)
    {
        $this->mimeType = $mimeType;
        $this->size = $size;
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }


}