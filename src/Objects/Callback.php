<?php

declare(strict_types=1);

namespace Cortex\OpenApi\Objects;

use Cortex\OpenApi\Concerns\HasExtensions;
use Cortex\OpenApi\Contracts\Serializable;
use Cortex\OpenApi\Contracts\HasExtensionsInterface;

final class Callback implements Serializable, HasExtensionsInterface
{
    use HasExtensions;

    /**
     * @var array<string, PathItem>
     */
    private array $expressions = [];

    public static function create(): self
    {
        return new self();
    }

    public static function ref(string $name, ?string $summary = null, ?string $description = null): Reference
    {
        return Reference::callback($name, $summary, $description);
    }

    public function expression(string $runtimeExpression, PathItem $pathItem): self
    {
        $this->expressions[$runtimeExpression] = $pathItem;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $output = [];

        foreach ($this->expressions as $expression => $pathItem) {
            $output[$expression] = $pathItem->toArray();
        }

        foreach ($this->getExtensions() as $key => $value) {
            $output[$key] = $value;
        }

        return $output;
    }
}
