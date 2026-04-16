<?php

declare(strict_types=1);

use Cortex\JsonSchema\Schema;
use Cortex\OpenApi\Objects\Parameter;

covers(Parameter::class);

it('requires path parameters to be required', function (): void {
    $parameter = Parameter::path('id', Schema::string());

    expect($parameter->toArray())->toBe([
        'name' => 'id',
        'in' => 'path',
        'required' => true,
        'schema' => [
            'type' => 'string',
        ],
    ]);
});

it('builds a query parameter', function (): void {
    $parameter = Parameter::query('search', Schema::string())
        ->description('Filter term')
        ->deprecated(true);

    expect($parameter->toArray())->toBe([
        'name' => 'search',
        'in' => 'query',
        'description' => 'Filter term',
        'deprecated' => true,
        'schema' => [
            'type' => 'string',
        ],
    ]);
});

it('builds a header parameter', function (): void {
    $parameter = Parameter::header('X-Trace', Schema::string())->required(true);

    expect($parameter->toArray())->toBe([
        'name' => 'X-Trace',
        'in' => 'header',
        'required' => true,
        'schema' => [
            'type' => 'string',
        ],
    ]);
});

it('builds a cookie parameter', function (): void {
    $parameter = Parameter::cookie('session', Schema::string());

    expect($parameter->toArray())->toBe([
        'name' => 'session',
        'in' => 'cookie',
        'schema' => [
            'type' => 'string',
        ],
    ]);
});

it('emits style/explode/allowReserved/example', function (): void {
    $parameter = Parameter::query('tags', Schema::array())
        ->style('form')
        ->explode(true)
        ->allowReserved(true)
        ->example(['a', 'b']);

    expect($parameter->toArray())->toBe([
        'name' => 'tags',
        'in' => 'query',
        'style' => 'form',
        'explode' => true,
        'allowReserved' => true,
        'example' => ['a', 'b'],
        'schema' => [
            'type' => 'array',
        ],
    ]);
});

it('supports ref() shortcut', function (): void {
    expect(Parameter::ref('#/components/parameters/PageSize')->toArray())->toBe([
        '$ref' => '#/components/parameters/PageSize',
    ]);
});
