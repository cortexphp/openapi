<?php

declare(strict_types=1);

namespace Cortex\OpenApi\Concerns;

use InvalidArgumentException;

trait HasExtensions
{
    /**
     * @var array<string, mixed>
     */
    private array $extensions = [];

    /**
     * Set a specification extension. Pass $value = null to unset.
     *
     * Keys are emitted with an `x-` prefix; callers may supply the key with or without it.
     */
    public function x(string $key, mixed $value = null): static
    {
        if ($key === '') {
            throw new InvalidArgumentException('Extension key cannot be empty.');
        }

        $normalized = str_starts_with($key, 'x-') ? $key : 'x-' . $key;

        if ($value === null) {
            unset($this->extensions[$normalized]);

            return $this;
        }

        $this->extensions[$normalized] = $value;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getExtensions(): array
    {
        return $this->extensions;
    }
}
