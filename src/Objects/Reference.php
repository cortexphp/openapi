<?php

declare(strict_types=1);

namespace Cortex\OpenApi\Objects;

use InvalidArgumentException;
use Cortex\OpenApi\Contracts\Serializable;

final readonly class Reference implements Serializable
{
    private function __construct(
        private string $pointer,
        private ?string $summary,
        private ?string $description,
    ) {}

    public static function to(string $pointer, ?string $summary = null, ?string $description = null): self
    {
        if ($pointer === '') {
            throw new InvalidArgumentException('Reference pointer cannot be empty.');
        }

        return new self($pointer, $summary, $description);
    }

    public static function schema(string $name, ?string $summary = null, ?string $description = null): self
    {
        return self::to('#/components/schemas/' . $name, $summary, $description);
    }

    public static function response(string $name, ?string $summary = null, ?string $description = null): self
    {
        return self::to('#/components/responses/' . $name, $summary, $description);
    }

    public static function parameter(string $name, ?string $summary = null, ?string $description = null): self
    {
        return self::to('#/components/parameters/' . $name, $summary, $description);
    }

    public static function requestBody(string $name, ?string $summary = null, ?string $description = null): self
    {
        return self::to('#/components/requestBodies/' . $name, $summary, $description);
    }

    public static function header(string $name, ?string $summary = null, ?string $description = null): self
    {
        return self::to('#/components/headers/' . $name, $summary, $description);
    }

    public static function example(string $name, ?string $summary = null, ?string $description = null): self
    {
        return self::to('#/components/examples/' . $name, $summary, $description);
    }

    public static function link(string $name, ?string $summary = null, ?string $description = null): self
    {
        return self::to('#/components/links/' . $name, $summary, $description);
    }

    public static function callback(string $name, ?string $summary = null, ?string $description = null): self
    {
        return self::to('#/components/callbacks/' . $name, $summary, $description);
    }

    public static function securityScheme(string $name, ?string $summary = null, ?string $description = null): self
    {
        return self::to('#/components/securitySchemes/' . $name, $summary, $description);
    }

    public static function pathItem(string $name, ?string $summary = null, ?string $description = null): self
    {
        return self::to('#/components/pathItems/' . $name, $summary, $description);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $output = [
            '$ref' => $this->pointer,
        ];

        if ($this->summary !== null) {
            $output['summary'] = $this->summary;
        }

        if ($this->description !== null) {
            $output['description'] = $this->description;
        }

        return $output;
    }
}
