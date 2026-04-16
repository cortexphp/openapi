<?php

declare(strict_types=1);

namespace Cortex\OpenApi\Objects;

use Cortex\OpenApi\Concerns\BuildsArray;
use Cortex\OpenApi\Concerns\HasExtensions;
use Cortex\OpenApi\Contracts\Serializable;
use Cortex\JsonSchema\Contracts\JsonSchema;
use Cortex\OpenApi\Contracts\HasExtensionsInterface;

final class Response implements Serializable, HasExtensionsInterface
{
    use BuildsArray;
    use HasExtensions;

    /**
     * Default description used by the named constructors when the user does not override it.
     */
    private const array DEFAULT_DESCRIPTIONS = [
        '200' => 'OK',
        '201' => 'Created',
        '202' => 'Accepted',
        '204' => 'No Content',
        '400' => 'Bad Request',
        '401' => 'Unauthorized',
        '403' => 'Forbidden',
        '404' => 'Not Found',
        '409' => 'Conflict',
        '422' => 'Unprocessable Entity',
        '429' => 'Too Many Requests',
        '500' => 'Internal Server Error',
    ];

    private ?string $description = null;

    /**
     * @var array<string, Header|Reference>
     */
    private array $headers = [];

    /**
     * @var array<string, MediaType>
     */
    private array $content = [];

    /**
     * @var array<string, Link|Reference>
     */
    private array $links = [];

    private function __construct(
        private readonly string $statusCode,
    ) {
        $this->description = self::DEFAULT_DESCRIPTIONS[$statusCode] ?? null;
    }

    public static function status(int|string $statusCode): self
    {
        return new self((string) $statusCode);
    }

    public static function default(): self
    {
        return new self('default');
    }

    public static function ok(): self
    {
        return new self('200');
    }

    public static function created(): self
    {
        return new self('201');
    }

    public static function accepted(): self
    {
        return new self('202');
    }

    public static function noContent(): self
    {
        return new self('204');
    }

    public static function badRequest(): self
    {
        return new self('400');
    }

    public static function unauthorized(): self
    {
        return new self('401');
    }

    public static function forbidden(): self
    {
        return new self('403');
    }

    public static function notFound(): self
    {
        return new self('404');
    }

    public static function conflict(): self
    {
        return new self('409');
    }

    public static function unprocessable(): self
    {
        return new self('422');
    }

    public static function tooManyRequests(): self
    {
        return new self('429');
    }

    public static function internalServerError(): self
    {
        return new self('500');
    }

    public static function ref(string $pointer): Reference
    {
        return Reference::to($pointer);
    }

    public function getStatusCode(): string
    {
        return $this->statusCode;
    }

    public function description(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param array<string, Header|Reference> $headers
     */
    public function headers(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    public function header(string $name, Header|Reference $header): self
    {
        $this->headers[$name] = $header;

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
     * Shorthand for ->content(MediaType::json($schema)).
     *
     * @param JsonSchema|array<string, mixed>|Reference|null $schema
     */
    public function json(JsonSchema|array|Reference|null $schema = null): self
    {
        return $this->content(MediaType::json($schema));
    }

    /**
     * @param array<string, Link|Reference> $links
     */
    public function links(array $links): self
    {
        $this->links = $links;

        return $this;
    }

    public function link(string $name, Link|Reference $link): self
    {
        $this->links[$name] = $link;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->buildArray([
            'description' => $this->description,
            'headers' => $this->headers,
            'content' => $this->content,
            'links' => $this->links,
        ]);
    }
}
