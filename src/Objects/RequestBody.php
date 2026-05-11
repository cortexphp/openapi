<?php

declare(strict_types=1);

namespace Cortex\OpenApi\Objects;

use Cortex\OpenApi\Concerns\BuildsArray;
use Cortex\OpenApi\Concerns\HasExtensions;
use Cortex\OpenApi\Contracts\Serializable;
use Cortex\JsonSchema\Contracts\JsonSchema;
use Cortex\OpenApi\Contracts\HasExtensionsInterface;

final class RequestBody implements Serializable, HasExtensionsInterface
{
    use BuildsArray;
    use HasExtensions;

    private ?string $description = null;

    private ?bool $required = null;

    /**
     * @var array<string, MediaType>
     */
    private array $content = [];

    public static function create(): self
    {
        return new self();
    }

    public static function ref(string $name, ?string $summary = null, ?string $description = null): Reference
    {
        return Reference::requestBody($name, $summary, $description);
    }

    public function description(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function required(?bool $required = true): self
    {
        $this->required = $required;

        return $this;
    }

    public function content(MediaType ...$content): self
    {
        $this->content = [];

        foreach ($content as $mediaType) {
            $this->content[$mediaType->getContentType()] = $mediaType;
        }

        return $this;
    }

    /**
     * Shorthand for ->content(MediaType::json($schema)).
     *
     * @param JsonSchema|array<string, mixed>|Reference|null $schema
     */
    public function json(JsonSchema|array|Reference|null $schema = null): self
    {
        return $this->content(MediaType::json($schema));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->buildArray([
            'description' => $this->description,
            'required' => $this->required,
            'content' => $this->content,
        ]);
    }
}
