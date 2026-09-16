<?php

declare(strict_types=1);

use Cortex\JsonSchema\Schema;
use Cortex\OpenApi\Concerns\BuildsArray;
use Cortex\OpenApi\Concerns\HasExtensions;
use Cortex\OpenApi\Contracts\Serializable;
use Cortex\OpenApi\Contracts\HasExtensionsInterface;

covers(BuildsArray::class, HasExtensions::class);

final class BuildsArrayFixture implements HasExtensionsInterface
{
    use BuildsArray;
    use HasExtensions;

    /**
     * @param array<string, mixed> $fields
     *
     * @return array<string, mixed>
     */
    public function assemble(array $fields): array
    {
        return $this->buildArray($fields);
    }
}

final class BuildsArraySerializableFixture implements Serializable
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'a' => 1,
        ];
    }
}

final class BuildsArrayNoExtensionsFixture
{
    use BuildsArray;

    /**
     * @param array<string, mixed> $fields
     *
     * @return array<string, mixed>
     */
    public function assemble(array $fields): array
    {
        return $this->buildArray($fields);
    }
}

it('drops null fields', function (): void {
    $out = (new BuildsArrayFixture())->assemble([
        'title' => 'X',
        'version' => null,
    ]);

    expect($out)->toBe([
        'title' => 'X',
    ]);
});

it('drops empty arrays', function (): void {
    $out = (new BuildsArrayFixture())->assemble([
        'title' => 'X',
        'tags' => [],
    ]);

    expect($out)->toBe([
        'title' => 'X',
    ]);
});

it('unwraps a Serializable child', function (): void {
    $out = (new BuildsArrayFixture())->assemble([
        'info' => new BuildsArraySerializableFixture(),
    ]);

    expect($out)->toBe([
        'info' => [
            'a' => 1,
        ],
    ]);
});

it('unwraps a list of Serializable children preserving list semantics', function (): void {
    $out = (new BuildsArrayFixture())->assemble([
        'tags' => [
            new BuildsArraySerializableFixture(),
            new BuildsArraySerializableFixture(),
        ],
    ]);

    expect($out)->toBe([
        'tags' => [
            [
                'a' => 1,
            ],
            [
                'a' => 1,
            ],
        ],
    ]);
});

it('unwraps an associative array of Serializable children preserving keys', function (): void {
    $out = (new BuildsArrayFixture())->assemble([
        'paths' => [
            '/users' => new BuildsArraySerializableFixture(),
            '/pets' => new BuildsArraySerializableFixture(),
        ],
    ]);

    expect($out)->toBe([
        'paths' => [
            '/users' => [
                'a' => 1,
            ],
            '/pets' => [
                'a' => 1,
            ],
        ],
    ]);
});

it('merges vendor extensions into the output', function (): void {
    $fixture = new BuildsArrayFixture();
    $fixture->x('foo', 'bar');

    $out = $fixture->assemble([
        'title' => 'X',
    ]);

    expect($out)->toBe([
        'title' => 'X',
        'x-foo' => 'bar',
    ]);
});

it('preserves explicit false values', function (): void {
    $out = (new BuildsArrayFixture())->assemble([
        'deprecated' => false,
    ]);

    expect($out)->toBe([
        'deprecated' => false,
    ]);
});

it('preserves explicit zero values', function (): void {
    $out = (new BuildsArrayFixture())->assemble([
        'minimum' => 0,
    ]);

    expect($out)->toBe([
        'minimum' => 0,
    ]);
});

it('does not call getExtensions() on classes without HasExtensionsInterface', function (): void {
    $fixture = new BuildsArrayNoExtensionsFixture();

    // If the instanceof check were mutated to true, this would fatal-error
    // because BuildsArrayNoExtensionsFixture has no getExtensions() method.
    $out = $fixture->assemble([
        'title' => 'Test',
    ]);

    expect($out)->toBe([
        'title' => 'Test',
    ]);
});

it('unwraps a Cortex JsonSchema stripping $schema and title', function (): void {
    $stringSchema = Schema::string('IgnoredTitle')->minLength(2);

    // Reference: stand-alone toArray() would include both $schema URI and title.
    $standalone = $stringSchema->toArray();
    expect($standalone)->toHaveKey('$schema');
    expect($standalone)->toHaveKey('title', 'IgnoredTitle');

    $out = (new BuildsArrayFixture())->assemble([
        'schema' => $stringSchema,
    ]);

    expect($out)->toBe([
        'schema' => [
            'type' => 'string',
            'minLength' => 2,
        ],
    ]);
});

it('keeps a JsonSchema title that differs from the constructor argument', function (): void {
    $objectSchema = Schema::object('Consult')->title('consults');

    expect($objectSchema->getTitle())->toBe('consults')
        ->and($objectSchema->getInitialTitle())->toBe('Consult');

    $out = (new BuildsArrayFixture())->assemble([
        'schema' => $objectSchema,
    ]);

    expect($out)->toBe([
        'schema' => [
            'type' => 'object',
            'title' => 'consults',
        ],
    ]);
    expect($out['schema'])->not->toHaveKey('$schema');
});

it('keeps a JsonSchema title set on a schema that had no constructor title', function (): void {
    $stringSchema = Schema::string()->title('IsoDateTime');

    expect($stringSchema->getTitle())->toBe('IsoDateTime')
        ->and($stringSchema->getInitialTitle())->toBeNull();

    $out = (new BuildsArrayFixture())->assemble([
        'schema' => $stringSchema,
    ]);

    expect($out)->toBe([
        'schema' => [
            'type' => 'string',
            'title' => 'IsoDateTime',
        ],
    ]);
});

it('strips a JsonSchema title that merely restates the constructor argument', function (): void {
    $stringSchema = Schema::string('IsoDateTime')->title('IsoDateTime');

    $out = (new BuildsArrayFixture())->assemble([
        'schema' => $stringSchema,
    ]);

    expect($out)->toBe([
        'schema' => [
            'type' => 'string',
        ],
    ]);
});

it('strips $schema from nested item schemas', function (): void {
    $arraySchema = Schema::array()->items(Schema::object()->properties(Schema::string('name')));

    // Reference: cortexphp/json-schema serializes items itself and includes the URI.
    expect($arraySchema->toArray()['items'])->toHaveKey('$schema');

    $out = (new BuildsArrayFixture())->assemble([
        'schema' => $arraySchema,
    ]);

    expect($out)->toBe([
        'schema' => [
            'type' => 'array',
            'items' => [
                'type' => 'object',
                'properties' => [
                    'name' => [
                        'type' => 'string',
                    ],
                ],
            ],
        ],
    ]);
});

it('strips $schema from deeply nested schemas', function (): void {
    $out = (new BuildsArrayFixture())->assemble([
        'schema' => Schema::array()->items(
            Schema::object()->properties(
                Schema::array('tags')->items(Schema::string()),
            ),
        ),
    ]);

    $json = json_encode($out, JSON_THROW_ON_ERROR);

    expect($json)->not->toContain('$schema');
});

it('keeps a property that is itself named $schema', function (): void {
    $out = (new BuildsArrayFixture())->assemble([
        'schema' => Schema::object()->properties(Schema::string('$schema'), Schema::string('id')),
    ]);

    expect($out['schema']['properties'])->toHaveKeys(['$schema', 'id']);
});

it('leaves $schema in a raw array schema untouched', function (): void {
    $out = (new BuildsArrayFixture())->assemble([
        'schema' => [
            'type' => 'object',
            '$schema' => 'https://example.test/dialect',
        ],
    ]);

    expect($out['schema'])->toHaveKey('$schema', 'https://example.test/dialect');
});
