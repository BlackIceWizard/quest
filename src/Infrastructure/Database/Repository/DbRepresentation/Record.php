<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Repository\DbRepresentation;

class Record
{
    private RecordStatus $status;
    private array $data;
    private ?string $hash;

    private function __construct(RecordStatus $status, array $data, ?string $hash)
    {
        $this->status = $status;
        $this->data = $data;
        $this->hash = $hash;
    }

    public static function justLoaded(array $data): self
    {
        return new self(RecordStatus::JustLoaded, $data, self::calculateHash($data));
    }

    public static function previouslyLoaded(array $data, string $originalHash): self
    {
        $newHash = self::calculateHash($data);

        if ($originalHash === $newHash) {
            return new self(RecordStatus::NotChanged, $data, $originalHash);
        }

        return new self(RecordStatus::Changed, $data, $newHash);
    }

    public static function new(array $data): self
    {
        return new self(RecordStatus::New, $data, null);
    }

    public function status(): RecordStatus
    {
        return $this->status;
    }

    public function data(): array
    {
        return $this->data;
    }

    public function hash(): string
    {
        return $this->hash;
    }

    private static function calculateHash(array $data): string
    {
        ksort($data);

        return md5(json_encode($data));
    }
}