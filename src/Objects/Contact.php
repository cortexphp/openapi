<?php

declare(strict_types=1);

namespace Cortex\OpenApi\Objects;

use Cortex\OpenApi\Concerns\BuildsArray;
use Cortex\OpenApi\Concerns\HasExtensions;
use Cortex\OpenApi\Contracts\Serializable;

final class Contact implements Serializable
{
    use BuildsArray;
    use HasExtensions;

    private ?string $name = null;

    private ?string $url = null;

    private ?string $email = null;

    public static function create(): self
    {
        return new self();
    }

    public function name(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function url(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function email(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->buildArray([
            'name' => $this->name,
            'url' => $this->url,
            'email' => $this->email,
        ]);
    }
}
