<?php

declare(strict_types=1);

namespace Cortex\OpenApi;

use JsonException;
use Throwable;
use RuntimeException;
use Opis\JsonSchema\Helper;
use Opis\JsonSchema\Validator;
use Symfony\Component\Yaml\Yaml;
use Cortex\OpenApi\Objects\Tag;
use Cortex\OpenApi\Objects\Info;
use Cortex\OpenApi\Objects\Server;
use Cortex\OpenApi\Enums\OpenApiVersion;
use Cortex\OpenApi\Objects\PathItem;
use Cortex\OpenApi\Objects\Reference;
use Cortex\OpenApi\Objects\Components;
use Cortex\OpenApi\Objects\ExternalDocs;
use Cortex\OpenApi\Concerns\BuildsArray;
use Cortex\OpenApi\Concerns\HasExtensions;
use Cortex\OpenApi\Contracts\Serializable;
use Opis\JsonSchema\Errors\ErrorFormatter;
use Cortex\OpenApi\Objects\SecurityRequirement;
use Cortex\OpenApi\Exceptions\ValidationException;

final class OpenApi implements Serializable
{
    use BuildsArray;
    use HasExtensions;

    private ?Info $info = null;

    private ?string $jsonSchemaDialect = null;

    /**
     * @var array<int, Server>
     */
    private array $servers = [];

    /**
     * @var array<string, PathItem>
     */
    private array $paths = [];

    /**
     * @var array<string, PathItem|Reference>
     */
    private array $webhooks = [];

    private ?Components $components = null;

    /**
     * @var array<int, SecurityRequirement>
     */
    private array $security = [];

    /**
     * @var array<int, Tag>
     */
    private array $tags = [];

    private ?ExternalDocs $externalDocs = null;

    private function __construct(
        private readonly OpenApiVersion $version,
    ) {}

    public static function create(?OpenApiVersion $version = null): self
    {
        return new self($version ?? OpenApiVersion::default());
    }

    public function getVersion(): OpenApiVersion
    {
        return $this->version;
    }

    public function info(Info $info): self
    {
        $this->info = $info;

        return $this;
    }

    public function jsonSchemaDialect(?string $jsonSchemaDialect): self
    {
        $this->jsonSchemaDialect = $jsonSchemaDialect;

        return $this;
    }

    public function servers(Server ...$servers): self
    {
        $this->servers = array_values($servers);

        return $this;
    }

    public function paths(PathItem ...$paths): self
    {
        $this->paths = [];

        foreach ($paths as $pathItem) {
            $this->paths[$pathItem->getPath()] = $pathItem;
        }

        return $this;
    }

    /**
     * @param array<string, PathItem|Reference> $webhooks
     */
    public function webhooks(array $webhooks): self
    {
        $this->webhooks = $webhooks;

        return $this;
    }

    public function components(?Components $components): self
    {
        $this->components = $components;

        return $this;
    }

    public function security(SecurityRequirement ...$security): self
    {
        $this->security = array_values($security);

        return $this;
    }

    public function tags(Tag ...$tags): self
    {
        $this->tags = array_values($tags);

        return $this;
    }

    public function externalDocs(?ExternalDocs $externalDocs): self
    {
        $this->externalDocs = $externalDocs;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $paths = [];

        foreach ($this->paths as $path => $pathItem) {
            $paths[$path] = $pathItem->toArray();
        }

        return $this->buildArray([
            'openapi' => $this->version->value,
            'info' => $this->info,
            'jsonSchemaDialect' => $this->jsonSchemaDialect,
            'servers' => $this->servers,
            'paths' => $paths,
            'webhooks' => $this->webhooks,
            'components' => $this->components,
            'security' => $this->security,
            'tags' => $this->tags,
            'externalDocs' => $this->externalDocs,
        ]);
    }

    public function toJson(int $flags = 0): string
    {
        try {
            return json_encode($this->toArray(), $flags | JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new RuntimeException('Failed to encode OpenAPI document as JSON: ' . $e->getMessage(), previous: $e);
        }
    }

    public function toYaml(int $inline = 10, int $indent = 2): string
    {
        if (! class_exists(Yaml::class)) {
            throw new RuntimeException(
                'YAML output requires symfony/yaml. Install with: composer require symfony/yaml',
            );
        }

        return Yaml::dump($this->toArray(), $inline, $indent);
    }

    /**
     * Validate the document against the official OpenAPI meta-schema.
     *
     * The meta-schema relies on $defs/$ref composition that cortex/json-schema's builder
     * does not currently preserve on round-trip, so validation is delegated directly to
     * opis/json-schema (a transitive dependency of cortex/json-schema).
     *
     * @throws ValidationException when the document does not conform to the spec
     */
    public function validate(): void
    {
        $metaPath = __DIR__ . '/../resources/schemas/' . $this->version->value . '.json';

        if (! is_file($metaPath)) {
            throw new ValidationException(sprintf(
                'OpenAPI meta-schema not found for version %s at %s.',
                $this->version->value,
                $metaPath,
            ));
        }

        $contents = file_get_contents($metaPath);

        if ($contents === false) {
            throw new ValidationException(sprintf(
                'Could not read OpenAPI meta-schema at %s.',
                $metaPath,
            ));
        }

        // Opis's $dynamicRef resolution incorrectly walks up to the ROOT meta-schema scope
        // when no outer $dynamicAnchor:meta is defined, causing spurious "missing openapi/info"
        // errors on every embedded Schema Object. Rewriting the dynamic ref to a regular $ref
        // preserves the structural-only Schema Object check the OpenAPI spec actually defines.
        $metaSchema = str_replace(
            '"$dynamicRef": "#meta"',
            '"$ref": "#/$defs/schema"',
            $contents,
        );

        $validator = new Validator();
        $validator->parser()->setOption('defaultDraft', '2020-12');
        // Disable default-injection: opis writes schema `default` values into the instance during
        // validation, which then get flagged by `unevaluatedProperties: false` on nested branches
        // (e.g. `allowReserved`, `allowEmptyValue`) that only appear in conditional `if/then` defs.
        $validator->parser()->setOption('allowDefaults', false);

        try {
            $result = $validator->validate(
                Helper::toJSON($this->toArray()),
                $metaSchema,
            );
        } catch (Throwable $e) {
            throw new ValidationException($e->getMessage(), previous: $e);
        }

        if ($result->hasError()) {
            $error = $result->error();
            $formatted = (new ErrorFormatter())->format($error);
            throw new ValidationException(
                'OpenAPI document failed meta-schema validation: ' . json_encode($formatted, JSON_UNESCAPED_SLASHES),
            );
        }
    }
}
