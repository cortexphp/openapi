<?php

declare(strict_types=1);

namespace Cortex\OpenApi\Objects;

use Cortex\OpenApi\Concerns\BuildsArray;
use Cortex\OpenApi\Concerns\HasExtensions;
use Cortex\OpenApi\Contracts\Serializable;
use Cortex\OpenApi\Contracts\HasExtensionsInterface;

final class OAuthFlow implements Serializable, HasExtensionsInterface
{
    use BuildsArray;
    use HasExtensions;

    private ?string $authorizationUrl = null;

    private ?string $tokenUrl = null;

    private ?string $refreshUrl = null;

    /**
     * @var array<string, string>
     */
    private array $scopes = [];

    public static function create(): self
    {
        return new self();
    }

    public function authorizationUrl(?string $authorizationUrl): self
    {
        $this->authorizationUrl = $authorizationUrl;

        return $this;
    }

    public function tokenUrl(?string $tokenUrl): self
    {
        $this->tokenUrl = $tokenUrl;

        return $this;
    }

    public function refreshUrl(?string $refreshUrl): self
    {
        $this->refreshUrl = $refreshUrl;

        return $this;
    }

    /**
     * @param array<string, string> $scopes
     */
    public function scopes(array $scopes): self
    {
        $this->scopes = $scopes;

        return $this;
    }

    public function scope(string $name, string $description): self
    {
        $this->scopes[$name] = $description;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->buildArray([
            'authorizationUrl' => $this->authorizationUrl,
            'tokenUrl' => $this->tokenUrl,
            'refreshUrl' => $this->refreshUrl,
            'scopes' => $this->scopes,
        ]);
    }
}
