<?php

declare(strict_types=1);

namespace Cortex\OpenApi\Objects;

use Cortex\OpenApi\Concerns\BuildsArray;
use Cortex\OpenApi\Concerns\HasExtensions;
use Cortex\OpenApi\Contracts\Serializable;
use Cortex\OpenApi\Contracts\HasExtensionsInterface;

final class Encoding implements Serializable, HasExtensionsInterface
{
    use BuildsArray;
    use HasExtensions;

    private ?string $contentType = null;

    /**
     * @var array<string, Header|Reference>
     */
    private array $headers = [];

    private ?string $style = null;

    private ?bool $explode = null;

    private ?bool $allowReserved = null;

    public static function create(): self
    {
        return new self();
    }

    public function contentType(?string $contentType): self
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * @param array<string, Header|Reference> $headers
     */
    public function headers(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    public function header(string $name, Header|Reference $header): self
    {
        $this->headers[$name] = $header;

        return $this;
    }

    public function style(?string $style): self
    {
        $this->style = $style;

        return $this;
    }

    public function explode(?bool $explode = true): self
    {
        $this->explode = $explode;

        return $this;
    }

    public function allowReserved(?bool $allowReserved = true): self
    {
        $this->allowReserved = $allowReserved;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->buildArray([
            'contentType' => $this->contentType,
            'headers' => $this->headers,
            'style' => $this->style,
            'explode' => $this->explode,
            'allowReserved' => $this->allowReserved,
        ]);
    }
}
