<?php

declare(strict_types=1);

namespace Cortex\OpenApi\Objects;

use InvalidArgumentException;
use Cortex\OpenApi\Contracts\Serializable;

final class Reference implements Serializable
{
    private function __construct(
        private readonly string $pointer,
        private readonly ?string $summary,
        private readonly ?string $description,
    ) {}

    public static function to(string $pointer, ?string $summary = null, ?string $description = null): self
    {
        if ($pointer === '') {
            throw new InvalidArgumentException('Reference pointer cannot be empty.');
        }

        return new self($pointer, $summary, $description);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $output = ['$ref' => $this->pointer];

        if ($this->summary !== null) {
            $output['summary'] = $this->summary;
        }

        if ($this->description !== null) {
            $output['description'] = $this->description;
        }

        return $output;
    }
}
