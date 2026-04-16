<?php

declare(strict_types=1);

namespace Cortex\OpenApi\Objects;

use Cortex\OpenApi\Concerns\BuildsArray;
use Cortex\OpenApi\Concerns\HasExtensions;
use Cortex\OpenApi\Contracts\Serializable;
use Cortex\OpenApi\Contracts\HasExtensionsInterface;

final class Xml implements Serializable, HasExtensionsInterface
{
    use BuildsArray;
    use HasExtensions;

    private ?string $name = null;

    private ?string $namespace = null;

    private ?string $prefix = null;

    private ?bool $attribute = null;

    private ?bool $wrapped = null;

    public static function create(): self
    {
        return new self();
    }

    public function name(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function namespace(?string $namespace): self
    {
        $this->namespace = $namespace;

        return $this;
    }

    public function prefix(?string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function attribute(?bool $attribute = true): self
    {
        $this->attribute = $attribute;

        return $this;
    }

    public function wrapped(?bool $wrapped = true): self
    {
        $this->wrapped = $wrapped;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->buildArray([
            'name' => $this->name,
            'namespace' => $this->namespace,
            'prefix' => $this->prefix,
            'attribute' => $this->attribute,
            'wrapped' => $this->wrapped,
        ]);
    }
}
