<?php

declare(strict_types=1);

namespace Cortex\OpenApi\Objects;

use Cortex\OpenApi\Concerns\BuildsArray;
use Cortex\OpenApi\Concerns\HasExtensions;
use Cortex\OpenApi\Contracts\Serializable;

final class License implements Serializable
{
    use BuildsArray;
    use HasExtensions;

    private ?string $identifier = null;

    private ?string $url = null;

    private function __construct(
        private readonly string $name,
    ) {}

    public static function create(string $name): self
    {
        return new self($name);
    }

    /**
     * SPDX license expression (OpenAPI 3.1 addition). Mutually exclusive with url() per spec.
     */
    public function identifier(?string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function url(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->buildArray([
            'name' => $this->name,
            'identifier' => $this->identifier,
            'url' => $this->url,
        ]);
    }
}
