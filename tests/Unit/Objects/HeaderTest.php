<?php

declare(strict_types=1);

use Cortex\JsonSchema\Schema;
use Cortex\OpenApi\Objects\Header;

it('emits nothing by default', function (): void {
    expect(Header::create()->toArray())->toBe([]);
});

it('emits a schema-based header', function (): void {
    $header = Header::create()
        ->description('Rate limit remaining')
        ->required(true)
        ->schema(Schema::integer()->minimum(0));

    expect($header->toArray())->toBe([
        'description' => 'Rate limit remaining',
        'required' => true,
        'schema' => ['type' => 'integer', 'minimum' => 0],
    ]);
});

it('supports deprecated and example', function (): void {
    $header = Header::create()
        ->deprecated(true)
        ->example('abc123');

    expect($header->toArray())->toBe([
        'deprecated' => true,
        'example' => 'abc123',
    ]);
});
