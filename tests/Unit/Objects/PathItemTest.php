<?php

declare(strict_types=1);

use Cortex\JsonSchema\Schema;
use Cortex\OpenApi\Objects\Server;
use Cortex\OpenApi\Objects\PathItem;
use Cortex\OpenApi\Objects\Operation;
use Cortex\OpenApi\Objects\Parameter;

covers(PathItem::class);

it('knows its own path', function (): void {
    expect(PathItem::create('/users')->getPath())->toBe('/users');
});

it('emits summary and description', function (): void {
    $pathItem = PathItem::create('/users')
        ->summary('User collection')
        ->description('Operations on users');

    expect($pathItem->toArray())->toBe([
        'summary' => 'User collection',
        'description' => 'Operations on users',
    ]);
});

it('emits operations keyed by HTTP method', function (): void {
    $pathItem = PathItem::create('/users/{id}')
        ->operations(
            Operation::get()->operationId('users.show'),
            Operation::delete()->operationId('users.destroy'),
        );

    expect($pathItem->toArray())->toBe([
        'get' => [
            'operationId' => 'users.show',
        ],
        'delete' => [
            'operationId' => 'users.destroy',
        ],
    ]);
});

it('emits shared parameters and servers', function (): void {
    $pathItem = PathItem::create('/users/{id}')
        ->parameters(Parameter::path('id', Schema::string()))
        ->servers(Server::create('https://api.example.com'));

    expect($pathItem->toArray())->toBe([
        'parameters' => [
            [
                'name' => 'id',
                'in' => 'path',
                'required' => true,
                'schema' => [
                    'type' => 'string',
                ],
            ],
        ],
        'servers' => [
            [
                'url' => 'https://api.example.com',
            ],
        ],
    ]);
});

it('supports ref() shortcut', function (): void {
    expect(PathItem::ref('UserById')->toArray())->toBe([
        '$ref' => '#/components/pathItems/UserById',
    ]);
});

it('emits vendor extensions', function (): void {
    $pathItem = PathItem::create('/users')
        ->x('internal', true);

    expect($pathItem->toArray())->toBe([
        'x-internal' => true,
    ]);
});

it('servers() preserves values as a list', function (): void {
    $pathItem = PathItem::create('/users')
        ->servers(
            Server::create('https://api.example.com'),
            Server::create('https://staging.example.com'),
        );

    $result = $pathItem->toArray();
    expect($result['servers'])->toBe([
        [
            'url' => 'https://api.example.com',
        ],
        [
            'url' => 'https://staging.example.com',
        ],
    ]);
    expect(array_is_list($result['servers']))->toBeTrue();
});

it('parameters() preserves values as a list', function (): void {
    $pathItem = PathItem::create('/users/{id}')
        ->parameters(
            Parameter::path('id', Schema::string()),
            Parameter::query('include', Schema::string()),
        );

    $result = $pathItem->toArray();
    expect(array_is_list($result['parameters']))->toBeTrue();
    expect($result['parameters'])->toHaveCount(2);
});
