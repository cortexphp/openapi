<?php

declare(strict_types=1);

use Cortex\OpenApi\Concerns\HasExtensions;

/**
 * Fixture consumer used purely to exercise the HasExtensions trait.
 */
final class HasExtensionsFixture
{
    use HasExtensions;
}

it('prefixes extension keys with x- by default', function (): void {
    $fixture = new HasExtensionsFixture();
    $fixture->x('foo', 'bar');

    expect($fixture->getExtensions())->toBe(['x-foo' => 'bar']);
});

it('accepts an already x- prefixed key without double-prefixing', function (): void {
    $fixture = new HasExtensionsFixture();
    $fixture->x('x-foo', 'bar');

    expect($fixture->getExtensions())->toBe(['x-foo' => 'bar']);
});

it('supports arbitrary values including nested arrays and objects', function (): void {
    $fixture = new HasExtensionsFixture();
    $fixture->x('items', ['a', 'b', 'c']);
    $fixture->x('meta', (object) ['count' => 3]);

    expect($fixture->getExtensions())->toEqual([
        'x-items' => ['a', 'b', 'c'],
        'x-meta' => (object) ['count' => 3],
    ]);
});

it('unsets an extension when value is null', function (): void {
    $fixture = new HasExtensionsFixture();
    $fixture->x('foo', 'bar');
    $fixture->x('keep', 'alive');
    $fixture->x('foo');

    expect($fixture->getExtensions())->toBe(['x-keep' => 'alive']);
});

it('returns $this for chaining', function (): void {
    $fixture = new HasExtensionsFixture();

    expect($fixture->x('foo', 'bar'))->toBe($fixture);
});

it('rejects an empty key', function (): void {
    (new HasExtensionsFixture())->x('');
})->throws(InvalidArgumentException::class);
