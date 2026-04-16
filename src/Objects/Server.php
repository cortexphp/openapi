<?php

declare(strict_types=1);

namespace Cortex\OpenApi\Objects;

use Cortex\OpenApi\Concerns\BuildsArray;
use Cortex\OpenApi\Concerns\HasExtensions;
use Cortex\OpenApi\Contracts\Serializable;
use Cortex\OpenApi\Contracts\HasExtensionsInterface;

final class Server implements Serializable, HasExtensionsInterface
{
    use BuildsArray;
    use HasExtensions;

    private ?string $description = null;

    /**
     * @var array<string, ServerVariable>
     */
    private array $variables = [];

    private function __construct(
        private readonly string $url,
    ) {}

    public static function create(string $url): self
    {
        return new self($url);
    }

    public function description(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function variables(ServerVariable ...$variables): self
    {
        $this->variables = [];

        foreach ($variables as $variable) {
            $this->variables[$variable->getName()] = $variable;
        }

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->buildArray([
            'url' => $this->url,
            'description' => $this->description,
            'variables' => $this->variables,
        ]);
    }
}
