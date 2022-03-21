<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Mapping;

trait MetaDataTrait
{
    private string $stateHash;

    public function __construct(string $stateHash)
    {
        $this->stateHash = $stateHash;
    }

    public function stateHash(): string
    {
        return $this->stateHash;
    }
}