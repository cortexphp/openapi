<?php

declare(strict_types=1);

namespace Cortex\OpenApi\Objects;

use Cortex\OpenApi\Enums\In;
use Cortex\OpenApi\Enums\SecuritySchemeType;
use Cortex\OpenApi\Concerns\BuildsArray;
use Cortex\OpenApi\Concerns\HasExtensions;
use Cortex\OpenApi\Contracts\Serializable;

final class SecurityScheme implements Serializable
{
    use BuildsArray;
    use HasExtensions;

    private ?string $description = null;

    private ?string $name = null;

    private ?In $in = null;

    private ?string $scheme = null;

    private ?string $bearerFormat = null;

    private ?OAuthFlows $flows = null;

    private ?string $openIdConnectUrl = null;

    private function __construct(
        private readonly SecuritySchemeType $type,
    ) {}

    public static function apiKey(string $name, In $in): self
    {
        $scheme = new self(SecuritySchemeType::ApiKey);
        $scheme->name = $name;
        $scheme->in = $in;

        return $scheme;
    }

    public static function http(string $scheme): self
    {
        $instance = new self(SecuritySchemeType::Http);
        $instance->scheme = $scheme;

        return $instance;
    }

    public static function oauth2(?OAuthFlows $flows = null): self
    {
        $scheme = new self(SecuritySchemeType::OAuth2);
        $scheme->flows = $flows;

        return $scheme;
    }

    public static function openIdConnect(string $openIdConnectUrl): self
    {
        $scheme = new self(SecuritySchemeType::OpenIdConnect);
        $scheme->openIdConnectUrl = $openIdConnectUrl;

        return $scheme;
    }

    public static function mutualTls(): self
    {
        return new self(SecuritySchemeType::MutualTls);
    }

    public static function ref(string $pointer): Reference
    {
        return Reference::to($pointer);
    }

    public function getType(): SecuritySchemeType
    {
        return $this->type;
    }

    public function description(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function name(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function in(?In $in): self
    {
        $this->in = $in;

        return $this;
    }

    public function scheme(?string $scheme): self
    {
        $this->scheme = $scheme;

        return $this;
    }

    public function bearerFormat(?string $bearerFormat): self
    {
        $this->bearerFormat = $bearerFormat;

        return $this;
    }

    public function flows(?OAuthFlows $flows): self
    {
        $this->flows = $flows;

        return $this;
    }

    public function openIdConnectUrl(?string $openIdConnectUrl): self
    {
        $this->openIdConnectUrl = $openIdConnectUrl;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->buildArray([
            'type' => $this->type->value,
            'description' => $this->description,
            'name' => $this->name,
            'in' => $this->in?->value,
            'scheme' => $this->scheme,
            'bearerFormat' => $this->bearerFormat,
            'flows' => $this->flows,
            'openIdConnectUrl' => $this->openIdConnectUrl,
        ]);
    }
}
