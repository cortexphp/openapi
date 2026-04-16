<?php

declare(strict_types=1);

namespace Cortex\OpenApi\Objects;

use Cortex\OpenApi\Concerns\BuildsArray;
use Cortex\OpenApi\Concerns\HasExtensions;
use Cortex\OpenApi\Contracts\Serializable;
use Cortex\JsonSchema\Contracts\JsonSchema;
use Cortex\OpenApi\Contracts\HasExtensionsInterface;

final class Components implements Serializable, HasExtensionsInterface
{
    use BuildsArray;
    use HasExtensions;

    /**
     * @var array<string, JsonSchema|array<string, mixed>|Reference>
     */
    private array $schemas = [];

    /**
     * @var array<string, Response|Reference>
     */
    private array $responses = [];

    /**
     * @var array<string, Parameter|Reference>
     */
    private array $parameters = [];

    /**
     * @var array<string, Example|Reference>
     */
    private array $examples = [];

    /**
     * @var array<string, RequestBody|Reference>
     */
    private array $requestBodies = [];

    /**
     * @var array<string, Header|Reference>
     */
    private array $headers = [];

    /**
     * @var array<string, SecurityScheme|Reference>
     */
    private array $securitySchemes = [];

    /**
     * @var array<string, Link|Reference>
     */
    private array $links = [];

    /**
     * @var array<string, Callback|Reference>
     */
    private array $callbacks = [];

    /**
     * @var array<string, PathItem|Reference>
     */
    private array $pathItems = [];

    public static function create(): self
    {
        return new self();
    }

    /**
     * @param JsonSchema|array<string, mixed>|Reference $schema
     */
    public function schema(string $name, JsonSchema|array|Reference $schema): self
    {
        $this->schemas[$name] = $schema;

        return $this;
    }

    public function response(string $name, Response|Reference $response): self
    {
        $this->responses[$name] = $response;

        return $this;
    }

    public function parameter(string $name, Parameter|Reference $parameter): self
    {
        $this->parameters[$name] = $parameter;

        return $this;
    }

    public function example(string $name, Example|Reference $example): self
    {
        $this->examples[$name] = $example;

        return $this;
    }

    public function requestBody(string $name, RequestBody|Reference $requestBody): self
    {
        $this->requestBodies[$name] = $requestBody;

        return $this;
    }

    public function header(string $name, Header|Reference $header): self
    {
        $this->headers[$name] = $header;

        return $this;
    }

    public function securityScheme(string $name, SecurityScheme|Reference $securityScheme): self
    {
        $this->securitySchemes[$name] = $securityScheme;

        return $this;
    }

    public function link(string $name, Link|Reference $link): self
    {
        $this->links[$name] = $link;

        return $this;
    }

    public function callback(string $name, Callback|Reference $callback): self
    {
        $this->callbacks[$name] = $callback;

        return $this;
    }

    public function pathItem(string $name, PathItem|Reference $pathItem): self
    {
        $this->pathItems[$name] = $pathItem;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->buildArray([
            'schemas' => $this->schemas,
            'responses' => $this->responses,
            'parameters' => $this->parameters,
            'examples' => $this->examples,
            'requestBodies' => $this->requestBodies,
            'headers' => $this->headers,
            'securitySchemes' => $this->securitySchemes,
            'links' => $this->links,
            'callbacks' => $this->callbacks,
            'pathItems' => $this->pathItems,
        ]);
    }
}
