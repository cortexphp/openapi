<?php

declare(strict_types=1);

namespace Cortex\OpenApi\Contracts;

interface Serializable
{
    /**
     * Convert the object to an OpenAPI-shape array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
