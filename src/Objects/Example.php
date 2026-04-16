<?php

declare(strict_types=1);

namespace Cortex\OpenApi\Objects;

use Cortex\OpenApi\Concerns\BuildsArray;
use Cortex\OpenApi\Concerns\HasExtensions;
use Cortex\OpenApi\Contracts\Serializable;
use Cortex\OpenApi\Contracts\HasExtensionsInterface;

final class Example implements Serializable, HasExtensionsInterface
{
    use BuildsArray;
    use HasExtensions;

    private ?string $summary = null;

    private ?string $description = null;

    private ?string $externalValue = null;

    private bool $hasValue = false;

    private mixed $value = null;

    public static function create(): self
    {
        return new self();
    }

    public static function ref(string $pointer): Reference
    {
        return Reference::to($pointer);
    }

    public function summary(?string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function description(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function value(mixed $value): self
    {
        $this->value = $value;
        $this->hasValue = true;

        return $this;
    }

    public function clearValue(): self
    {
        $this->value = null;
        $this->hasValue = false;

        return $this;
    }

    public function externalValue(?string $externalValue): self
    {
        $this->externalValue = $externalValue;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->buildArray([
            'summary' => $this->summary,
            'description' => $this->description,
            'value' => $this->value,
            'externalValue' => $this->externalValue,
        ], $this->hasValue ? ['value'] : []);
    }
}
