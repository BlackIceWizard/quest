<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Specification;

interface EntitySpecification
{
    /**
     * @return class-string
     */
    public function className(): string;

    public function tableName(): string;

    public function referencedField();
}