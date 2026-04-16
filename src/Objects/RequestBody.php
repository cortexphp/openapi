<?php

declare(strict_types=1);

namespace Cortex\OpenApi\Objects;

use Cortex\OpenApi\Concerns\BuildsArray;
use Cortex\OpenApi\Concerns\HasExtensions;
use Cortex\OpenApi\Contracts\Serializable;

final class RequestBody implements Serializable
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

    public static function ref(string $pointer): Reference
    {
        return Reference::to($pointer);
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
