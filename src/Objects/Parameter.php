<?php

declare(strict_types=1);

namespace Cortex\OpenApi\Objects;

use Cortex\OpenApi\Enums\In;
use Cortex\OpenApi\Concerns\BuildsArray;
use Cortex\OpenApi\Concerns\HasExtensions;
use Cortex\OpenApi\Contracts\Serializable;
use Cortex\JsonSchema\Contracts\JsonSchema;

final class Parameter implements Serializable
{
    use BuildsArray;
    use HasExtensions;

    private ?string $description = null;

    private ?bool $required = null;

    private ?bool $deprecated = null;

    private ?bool $allowEmptyValue = null;

    private ?string $style = null;

    private ?bool $explode = null;

    private ?bool $allowReserved = null;

    /**
     * @var JsonSchema|array<string, mixed>|Reference|null
     */
    private JsonSchema|array|Reference|null $schema = null;

    private bool $hasExample = false;

    private mixed $example = null;

    /**
     * @var array<string, Example|Reference>
     */
    private array $examples = [];

    /**
     * @var array<string, MediaType>
     */
    private array $content = [];

    /**
     * @param JsonSchema|array<string, mixed>|Reference|null $schema
     */
    private function __construct(
        private readonly string $name,
        private readonly In $in,
        JsonSchema|array|Reference|null $schema = null,
    ) {
        $this->schema = $schema;

        if ($in === In::Path) {
            $this->required = true;
        }
    }

    /**
     * @param JsonSchema|array<string, mixed>|Reference|null $schema
     */
    public static function path(string $name, JsonSchema|array|Reference|null $schema = null): self
    {
        return new self($name, In::Path, $schema);
    }

    /**
     * @param JsonSchema|array<string, mixed>|Reference|null $schema
     */
    public static function query(string $name, JsonSchema|array|Reference|null $schema = null): self
    {
        return new self($name, In::Query, $schema);
    }

    /**
     * @param JsonSchema|array<string, mixed>|Reference|null $schema
     */
    public static function header(string $name, JsonSchema|array|Reference|null $schema = null): self
    {
        return new self($name, In::Header, $schema);
    }

    /**
     * @param JsonSchema|array<string, mixed>|Reference|null $schema
     */
    public static function cookie(string $name, JsonSchema|array|Reference|null $schema = null): self
    {
        return new self($name, In::Cookie, $schema);
    }

    public static function ref(string $pointer): Reference
    {
        return Reference::to($pointer);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIn(): In
    {
        return $this->in;
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

    public function deprecated(?bool $deprecated = true): self
    {
        $this->deprecated = $deprecated;

        return $this;
    }

    public function allowEmptyValue(?bool $allowEmptyValue = true): self
    {
        $this->allowEmptyValue = $allowEmptyValue;

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
     * @param JsonSchema|array<string, mixed>|Reference|null $schema
     */
    public function schema(JsonSchema|array|Reference|null $schema): self
    {
        $this->schema = $schema;

        return $this;
    }

    public function example(mixed $example): self
    {
        $this->example = $example;
        $this->hasExample = true;

        return $this;
    }

    /**
     * @param array<string, Example|Reference> $examples
     */
    public function examples(array $examples): self
    {
        $this->examples = $examples;

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
        $output = $this->buildArray([
            'name' => $this->name,
            'in' => $this->in->value,
            'description' => $this->description,
            'required' => $this->required,
            'deprecated' => $this->deprecated,
            'allowEmptyValue' => $this->allowEmptyValue,
            'style' => $this->style,
            'explode' => $this->explode,
            'allowReserved' => $this->allowReserved,
            'examples' => $this->examples,
            'schema' => $this->schema,
            'content' => $this->content,
        ]);

        if ($this->hasExample) {
            // Insert example before examples/schema/content so conventional order is preserved,
            // even when the example value is literal null (which buildArray would otherwise drop).
            $reordered = [];

            foreach ($output as $key => $value) {
                if (! array_key_exists('example', $reordered)
                    && in_array($key, ['examples', 'schema', 'content'], true)
                ) {
                    $reordered['example'] = $this->example;
                }

                $reordered[$key] = $value;
            }

            if (! array_key_exists('example', $reordered)) {
                $reordered['example'] = $this->example;
            }

            return $reordered;
        }

        return $output;
    }
}
