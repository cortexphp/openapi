<?php

declare(strict_types=1);

use Cortex\JsonSchema\Schema;
use Cortex\OpenApi\Objects\Header;

covers(Header::class);

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
        'schema' => [
            'type' => 'integer',
            'minimum' => 0,
        ],
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

it('required() defaults to true', function (): void {
    expect(Header::create()->required()->toArray())->toBe(['required' => true]);
});

it('deprecated() defaults to true', function (): void {
    expect(Header::create()->deprecated()->toArray())->toBe(['deprecated' => true]);
});

it('emits all optional fields when set', function (): void {
    $header = Header::create()
        ->allowEmptyValue(true)
        ->style('simple')
        ->explode(true)
        ->allowReserved(true)
        ->schema(Schema::string());

    expect($header->toArray())->toBe([
        'allowEmptyValue' => true,
        'style' => 'simple',
        'explode' => true,
        'allowReserved' => true,
        'schema' => ['type' => 'string'],
    ]);
});

it('explode() defaults to true', function (): void {
    expect(Header::create()->explode()->toArray())->toBe(['explode' => true]);
});

it('allowReserved() defaults to true', function (): void {
    expect(Header::create()->allowReserved()->toArray())->toBe(['allowReserved' => true]);
});
