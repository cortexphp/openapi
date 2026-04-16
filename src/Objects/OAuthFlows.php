<?php

declare(strict_types=1);

namespace Cortex\OpenApi\Objects;

use Cortex\OpenApi\Concerns\BuildsArray;
use Cortex\OpenApi\Concerns\HasExtensions;
use Cortex\OpenApi\Contracts\Serializable;

final class OAuthFlows implements Serializable
{
    use BuildsArray;
    use HasExtensions;

    private ?OAuthFlow $implicit = null;

    private ?OAuthFlow $password = null;

    private ?OAuthFlow $clientCredentials = null;

    private ?OAuthFlow $authorizationCode = null;

    public static function create(): self
    {
        return new self();
    }

    public function implicit(?OAuthFlow $flow): self
    {
        $this->implicit = $flow;

        return $this;
    }

    public function password(?OAuthFlow $flow): self
    {
        $this->password = $flow;

        return $this;
    }

    public function clientCredentials(?OAuthFlow $flow): self
    {
        $this->clientCredentials = $flow;

        return $this;
    }

    public function authorizationCode(?OAuthFlow $flow): self
    {
        $this->authorizationCode = $flow;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->buildArray([
            'implicit' => $this->implicit,
            'password' => $this->password,
            'clientCredentials' => $this->clientCredentials,
            'authorizationCode' => $this->authorizationCode,
        ]);
    }
}
