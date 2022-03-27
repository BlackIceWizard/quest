<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Repository\DbRepresentation;

use JetBrains\PhpStorm\Pure;

class Record
{
    private RecordStatus $status;
    private array $data;
    private string $stateHash;

    public function __construct(RecordStatus $status, array $data)
    {
        $this->status = $status;
        $this->data = $data;
        $this->stateHash = $this->calculateStateHash();
    }

    #[Pure]
    public static function justLoaded(array $data): self
    {
        return new self(RecordStatus::JustLoaded, $data);
    }

    #[Pure]
    public static function changed(array $data): self
    {
        return new self(RecordStatus::Changed, $data);
    }

    #[Pure]
    public static function notChanged(array $data): self
    {
        return new self(RecordStatus::NotChanged, $data);
    }

    public function status(): RecordStatus
    {
        return $this->status;
    }

    public function data(): array
    {
        return $this->data;
    }

    public function stateHash(): string
    {
        return $this->stateHash;
    }

    private function calculateStateHash(): string
    {
        ksort($this->data);

        return md5(json_encode($this->data));
    }
}