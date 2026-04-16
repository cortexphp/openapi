<?php

declare(strict_types=1);

namespace Cortex\OpenApi\Objects;

use Cortex\OpenApi\Concerns\BuildsArray;
use Cortex\OpenApi\Concerns\HasExtensions;
use Cortex\OpenApi\Contracts\Serializable;
use Cortex\JsonSchema\Contracts\JsonSchema;

final class MediaType implements Serializable
{
    use BuildsArray;
    use HasExtensions;

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
     * @var array<string, Encoding>
     */
    private array $encoding = [];

    /**
     * @param JsonSchema|array<string, mixed>|Reference|null $schema
     */
    private function __construct(
        private readonly string $contentType,
        JsonSchema|array|Reference|null $schema = null,
    ) {
        $this->schema = $schema;
    }

    public static function of(string $contentType): self
    {
        return new self($contentType);
    }

    /**
     * @param JsonSchema|array<string, mixed>|Reference|null $schema
     */
    public static function json(JsonSchema|array|Reference|null $schema = null): self
    {
        return new self('application/json', $schema);
    }

    /**
     * @param JsonSchema|array<string, mixed>|Reference|null $schema
     */
    public static function xml(JsonSchema|array|Reference|null $schema = null): self
    {
        return new self('application/xml', $schema);
    }

    /**
     * @param JsonSchema|array<string, mixed>|Reference|null $schema
     */
    public static function form(JsonSchema|array|Reference|null $schema = null): self
    {
        return new self('application/x-www-form-urlencoded', $schema);
    }

    /**
     * @param JsonSchema|array<string, mixed>|Reference|null $schema
     */
    public static function multipart(JsonSchema|array|Reference|null $schema = null): self
    {
        return new self('multipart/form-data', $schema);
    }

    /**
     * @param JsonSchema|array<string, mixed>|Reference|null $schema
     */
    public static function text(JsonSchema|array|Reference|null $schema = null): self
    {
        return new self('text/plain', $schema);
    }

    /**
     * @param JsonSchema|array<string, mixed>|Reference|null $schema
     */
    public static function html(JsonSchema|array|Reference|null $schema = null): self
    {
        return new self('text/html', $schema);
    }

    /**
     * @param JsonSchema|array<string, mixed>|Reference|null $schema
     */
    public static function octetStream(JsonSchema|array|Reference|null $schema = null): self
    {
        return new self('application/octet-stream', $schema);
    }

    public function getContentType(): string
    {
        return $this->contentType;
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

    /**
     * @param array<string, Encoding> $encoding
     */
    public function encoding(array $encoding): self
    {
        $this->encoding = $encoding;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $output = $this->buildArray([
            'schema' => $this->schema,
            'examples' => $this->examples,
            'encoding' => $this->encoding,
        ]);

        if ($this->hasExample) {
            // Insert example before examples/encoding so conventional key order
            // is preserved, even when the value is literal null.
            $reordered = [];

            foreach ($output as $key => $value) {
                if (! array_key_exists('example', $reordered)
                    && in_array($key, ['examples', 'encoding'], true)
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
