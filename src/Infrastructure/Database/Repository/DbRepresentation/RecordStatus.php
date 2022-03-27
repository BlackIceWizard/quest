<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Repository\DbRepresentation;

enum RecordStatus
{
    case JustLoaded;
    case New;
    case NotChanged;
    case Changed;
}