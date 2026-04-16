<?php

declare(strict_types=1);

namespace Cortex\OpenApi\Objects;

use Cortex\OpenApi\Concerns\BuildsArray;
use Cortex\OpenApi\Concerns\HasExtensions;
use Cortex\OpenApi\Contracts\Serializable;
use Cortex\OpenApi\Contracts\HasExtensionsInterface;

final class Discriminator implements Serializable, HasExtensionsInterface
{
    use BuildsArray;
    use HasExtensions;

    /**
     * @var array<string, string>
     */
    private array $mapping = [];

    private function __construct(
        private readonly string $propertyName,
    ) {}

    public static function create(string $propertyName): self
    {
        return new self($propertyName);
    }

    /**
     * @param array<string, string> $mapping
     */
    public function mapping(array $mapping): self
    {
        $this->mapping = $mapping;

        return $this;
    }

    public function map(string $discriminatorValue, string $schemaRef): self
    {
        $this->mapping[$discriminatorValue] = $schemaRef;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->buildArray([
            'propertyName' => $this->propertyName,
            'mapping' => $this->mapping,
        ]);
    }
}
