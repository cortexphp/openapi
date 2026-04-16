<?php

declare(strict_types=1);

namespace Cortex\OpenApi\Objects;

use Cortex\OpenApi\Contracts\Serializable;

final readonly class SecurityRequirement implements Serializable
{
    /**
     * @param array<int, string> $scopes
     */
    private function __construct(
        private ?string $scheme,
        private array $scopes,
    ) {}

    /**
     * @param array<int, string> $scopes
     */
    public static function create(string $scheme, array $scopes = []): self
    {
        return new self($scheme, $scopes);
    }

    /**
     * Empty security requirement — applied to disable security for a specific operation.
     */
    public static function public(): self
    {
        return new self(null, []);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function toArray(): array
    {
        if ($this->scheme === null) {
            return [];
        }

        return [
            $this->scheme => $this->scopes,
        ];
    }
}
