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

    public function implicit(?OAuthFlow $oAuthFlow): self
    {
        $this->implicit = $oAuthFlow;

        return $this;
    }

    public function password(?OAuthFlow $oAuthFlow): self
    {
        $this->password = $oAuthFlow;

        return $this;
    }

    public function clientCredentials(?OAuthFlow $oAuthFlow): self
    {
        $this->clientCredentials = $oAuthFlow;

        return $this;
    }

    public function authorizationCode(?OAuthFlow $oAuthFlow): self
    {
        $this->authorizationCode = $oAuthFlow;

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
