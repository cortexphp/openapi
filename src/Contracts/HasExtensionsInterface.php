<?php

declare(strict_types=1);

namespace Cortex\OpenApi\Contracts;

interface HasExtensionsInterface
{
    /**
     * @return array<string, mixed>
     */
    public function getExtensions(): array;
}
