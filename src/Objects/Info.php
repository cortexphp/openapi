<?php

declare(strict_types=1);

namespace Cortex\OpenApi\Objects;

use Cortex\OpenApi\Concerns\BuildsArray;
use Cortex\OpenApi\Concerns\HasExtensions;
use Cortex\OpenApi\Contracts\Serializable;

final class Info implements Serializable
{
    use BuildsArray;
    use HasExtensions;

    private ?string $title = null;

    private ?string $summary = null;

    private ?string $description = null;

    private ?string $termsOfService = null;

    private ?Contact $contact = null;

    private ?License $license = null;

    private ?string $version = null;

    public static function create(): self
    {
        return new self();
    }

    public function title(?string $title): self
    {
        $this->title = $title;

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

    public function termsOfService(?string $termsOfService): self
    {
        $this->termsOfService = $termsOfService;

        return $this;
    }

    public function contact(?Contact $contact): self
    {
        $this->contact = $contact;

        return $this;
    }

    public function license(?License $license): self
    {
        $this->license = $license;

        return $this;
    }

    public function version(?string $version): self
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->buildArray([
            'title' => $this->title,
            'summary' => $this->summary,
            'description' => $this->description,
            'termsOfService' => $this->termsOfService,
            'contact' => $this->contact,
            'license' => $this->license,
            'version' => $this->version,
        ]);
    }
}
