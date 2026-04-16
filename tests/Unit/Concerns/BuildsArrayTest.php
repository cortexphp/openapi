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
