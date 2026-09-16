<?php

declare(strict_types=1);

use Cortex\OpenApi\Objects\Example;

covers(Example::class);

it('emits an empty array when no fields are set', function (): void {
    expect(Example::create()->toArray())->toBe([]);
});

it('emits every field when set', function (): void {
    $example = Example::create()
        ->summary('Sample user')
        ->description('A representative user')
        ->value([
            'id' => 1,
            'name' => 'Ada',
        ]);

    expect($example->toArray())->toBe([
        'summary' => 'Sample user',
        'description' => 'A representative user',
        'value' => [
            'id' => 1,
            'name' => 'Ada',
        ],
    ]);
});

it('emits externalValue when set', function (): void {
    $example = Example::create()->externalValue('https://example.com/sample.json');

    expect($example->toArray())->toBe([
        'externalValue' => 'https://example.com/sample.json',
    ]);
});

it('clears a value when explicitly cleared', function (): void {
    $example = Example::create()->value('a');

    expect($example->toArray())->toBe([
        'value' => 'a',
    ]);

    $example->clearValue();

    expect($example->toArray())->toBe([]);
});
