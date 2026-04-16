<?php

declare(strict_types=1);

namespace Cortex\OpenApi\Objects;

use Cortex\OpenApi\Concerns\BuildsArray;
use Cortex\OpenApi\Concerns\HasExtensions;
use Cortex\OpenApi\Contracts\Serializable;

final class Link implements Serializable
{
    use BuildsArray;
    use HasExtensions;

    private ?string $operationRef = null;

    private ?string $operationId = null;

    /**
     * @var array<string, mixed>
     */
    private array $parameters = [];

    private bool $hasRequestBody = false;

    private mixed $requestBody = null;

    private ?string $description = null;

    private ?Server $server = null;

    public static function create(): self
    {
        return new self();
    }

    public static function ref(string $pointer): Reference
    {
        return Reference::to($pointer);
    }

    public function operationRef(?string $operationRef): self
    {
        $this->operationRef = $operationRef;

        return $this;
    }

    public function operationId(?string $operationId): self
    {
        $this->operationId = $operationId;

        return $this;
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function parameters(array $parameters): self
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function requestBody(mixed $requestBody): self
    {
        $this->requestBody = $requestBody;
        $this->hasRequestBody = true;

        return $this;
    }

    public function description(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function server(?Server $server): self
    {
        $this->server = $server;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $output = $this->buildArray([
            'operationRef' => $this->operationRef,
            'operationId' => $this->operationId,
            'parameters' => $this->parameters,
            'description' => $this->description,
            'server' => $this->server,
        ]);

        if ($this->hasRequestBody) {
            // Insert requestBody after parameters (or before description/server) so the
            // conventional key order is preserved, even when the value is literal null.
            $reordered = [];

            foreach ($output as $key => $value) {
                $reordered[$key] = $value;

                if ($key === 'parameters' && ! array_key_exists('requestBody', $reordered)) {
                    $reordered['requestBody'] = $this->requestBody;
                }
            }

            if (! array_key_exists('requestBody', $reordered)) {
                // No parameters present — insert requestBody before description/server.
                $ordered = [];
                $inserted = false;

                foreach ($reordered as $key => $value) {
                    if (! $inserted && in_array($key, ['description', 'server'], true)) {
                        $ordered['requestBody'] = $this->requestBody;
                        $inserted = true;
                    }

                    $ordered[$key] = $value;
                }

                if (! $inserted) {
                    $ordered['requestBody'] = $this->requestBody;
                }

                return $ordered;
            }

            return $reordered;
        }

        return $output;
    }
}
