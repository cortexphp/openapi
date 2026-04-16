<?php

declare(strict_types=1);

namespace Cortex\OpenApi\Objects;

use Cortex\OpenApi\Concerns\BuildsArray;
use Cortex\OpenApi\Concerns\HasExtensions;
use Cortex\OpenApi\Contracts\Serializable;

final class PathItem implements Serializable
{
    use BuildsArray;
    use HasExtensions;

    private ?string $summary = null;

    private ?string $description = null;

    /**
     * @var array<string, Operation>
     */
    private array $operations = [];

    /**
     * @var array<int, Server>
     */
    private array $servers = [];

    /**
     * @var array<int, Parameter|Reference>
     */
    private array $parameters = [];

    private function __construct(
        private readonly string $path,
    ) {}

    public static function create(string $path): self
    {
        return new self($path);
    }

    public static function ref(string $pointer): Reference
    {
        return Reference::to($pointer);
    }

    public function getPath(): string
    {
        return $this->path;
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

    public function operations(Operation ...$operations): self
    {
        $this->operations = [];

        foreach ($operations as $operation) {
            $this->operations[$operation->getMethod()->value] = $operation;
        }

        return $this;
    }

    public function servers(Server ...$servers): self
    {
        $this->servers = array_values($servers);

        return $this;
    }

    public function parameters(Parameter|Reference ...$parameters): self
    {
        $this->parameters = array_values($parameters);

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $output = [];

        if ($this->summary !== null) {
            $output['summary'] = $this->summary;
        }

        if ($this->description !== null) {
            $output['description'] = $this->description;
        }

        foreach ($this->operations as $method => $operation) {
            $output[$method] = $operation->toArray();
        }

        if ($this->parameters !== []) {
            $output['parameters'] = array_map(
                fn (Parameter|Reference $parameter): array => $parameter->toArray(),
                $this->parameters,
            );
        }

        if ($this->servers !== []) {
            $output['servers'] = array_map(
                fn (Server $server): array => $server->toArray(),
                $this->servers,
            );
        }

        foreach ($this->getExtensions() as $key => $value) {
            $output[$key] = $value;
        }

        return $output;
    }
}
