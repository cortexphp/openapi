<?php

declare(strict_types=1);

namespace Cortex\OpenApi\Objects;

use Cortex\OpenApi\Concerns\BuildsArray;
use Cortex\OpenApi\Concerns\HasExtensions;
use Cortex\OpenApi\Contracts\Serializable;

final class ServerVariable implements Serializable
{
    use BuildsArray;
    use HasExtensions;

    /**
     * @var list<string>|null
     */
    private ?array $enum = null;

    private ?string $description = null;

    private function __construct(
        private readonly string $name,
        private readonly string $default,
    ) {}

    public static function create(string $name, string $default): self
    {
        return new self($name, $default);
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param list<string>|null $values
     */
    public function enum(?array $values): self
    {
        $this->enum = $values;

        return $this;
    }

    public function description(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->buildArray([
            'default' => $this->default,
            'enum' => $this->enum,
            'description' => $this->description,
        ]);
    }
}
