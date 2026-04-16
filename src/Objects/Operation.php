<?php

declare(strict_types=1);

namespace Cortex\OpenApi\Objects;

use Cortex\OpenApi\Enums\HttpMethod;
use Cortex\OpenApi\Concerns\BuildsArray;
use Cortex\OpenApi\Concerns\HasExtensions;
use Cortex\OpenApi\Contracts\Serializable;
use Cortex\OpenApi\Contracts\HasExtensionsInterface;

final class Operation implements Serializable, HasExtensionsInterface
{
    use BuildsArray;
    use HasExtensions;

    /**
     * @var array<int, string>
     */
    private array $tags = [];

    private ?string $summary = null;

    private ?string $description = null;

    private ?ExternalDocs $externalDocs = null;

    private ?string $operationId = null;

    /**
     * @var array<int, Parameter|Reference>
     */
    private array $parameters = [];

    private RequestBody|Reference|null $requestBody = null;

    /**
     * @var array<string, Response|Reference>
     */
    private array $responses = [];

    /**
     * @var array<string, Callback|Reference>
     */
    private array $callbacks = [];

    private ?bool $deprecated = null;

    /**
     * @var array<int, SecurityRequirement>
     */
    private array $security = [];

    /**
     * @var array<int, Server>
     */
    private array $servers = [];

    private function __construct(
        private readonly HttpMethod $httpMethod,
    ) {}

    public static function get(): self
    {
        return new self(HttpMethod::Get);
    }

    public static function put(): self
    {
        return new self(HttpMethod::Put);
    }

    public static function post(): self
    {
        return new self(HttpMethod::Post);
    }

    public static function delete(): self
    {
        return new self(HttpMethod::Delete);
    }

    public static function options(): self
    {
        return new self(HttpMethod::Options);
    }

    public static function head(): self
    {
        return new self(HttpMethod::Head);
    }

    public static function patch(): self
    {
        return new self(HttpMethod::Patch);
    }

    public static function trace(): self
    {
        return new self(HttpMethod::Trace);
    }

    public function getMethod(): HttpMethod
    {
        return $this->httpMethod;
    }

    public function tags(string ...$tags): self
    {
        $this->tags = array_values($tags);

        return $this;
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

    public function externalDocs(?ExternalDocs $externalDocs): self
    {
        $this->externalDocs = $externalDocs;

        return $this;
    }

    public function operationId(?string $operationId): self
    {
        $this->operationId = $operationId;

        return $this;
    }

    public function parameters(Parameter|Reference ...$parameters): self
    {
        $this->parameters = array_values($parameters);

        return $this;
    }

    public function requestBody(RequestBody|Reference|null $requestBody): self
    {
        $this->requestBody = $requestBody;

        return $this;
    }

    public function responses(Response|Reference ...$responses): self
    {
        $this->responses = [];

        foreach ($responses as $response) {
            if ($response instanceof Reference) {
                // References to response objects must be keyed by a user-supplied status; use ref() map directly instead.
                continue;
            }

            $this->responses[$response->getStatusCode()] = $response;
        }

        return $this;
    }

    /**
     * @param array<string, Response|Reference> $responses
     */
    public function responseMap(array $responses): self
    {
        $this->responses = $responses;

        return $this;
    }

    /**
     * @param array<string, Callback|Reference> $callbacks
     */
    public function callbacks(array $callbacks): self
    {
        $this->callbacks = $callbacks;

        return $this;
    }

    public function callback(string $name, Callback|Reference $callback): self
    {
        $this->callbacks[$name] = $callback;

        return $this;
    }

    public function deprecated(?bool $deprecated = true): self
    {
        $this->deprecated = $deprecated;

        return $this;
    }

    public function security(SecurityRequirement ...$security): self
    {
        $this->security = array_values($security);

        return $this;
    }

    public function servers(Server ...$servers): self
    {
        $this->servers = array_values($servers);

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->buildArray([
            'tags' => $this->tags,
            'summary' => $this->summary,
            'description' => $this->description,
            'externalDocs' => $this->externalDocs,
            'operationId' => $this->operationId,
            'parameters' => $this->parameters,
            'requestBody' => $this->requestBody,
            'responses' => $this->responses,
            'callbacks' => $this->callbacks,
            'deprecated' => $this->deprecated,
            'security' => $this->security,
            'servers' => $this->servers,
        ]);
    }
}
