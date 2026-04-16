<?php

declare(strict_types=1);

use Cortex\OpenApi\Objects\Xml;

covers(Xml::class);

it('emits nothing by default', function (): void {
    expect(Xml::create()->toArray())->toBe([]);
});

it('emits every field', function (): void {
    $xml = Xml::create()
        ->name('user')
        ->namespace('https://example.com/ns')
        ->prefix('ex')
        ->attribute(true)
        ->wrapped(true);

    expect($xml->toArray())->toBe([
        'name' => 'user',
        'namespace' => 'https://example.com/ns',
        'prefix' => 'ex',
        'attribute' => true,
        'wrapped' => true,
    ]);
});

it('attribute() defaults to true', function (): void {
    expect(Xml::create()->attribute()->toArray())->toBe(['attribute' => true]);
});

it('wrapped() defaults to true', function (): void {
    expect(Xml::create()->wrapped()->toArray())->toBe(['wrapped' => true]);
});
