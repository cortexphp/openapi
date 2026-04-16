<?php

declare(strict_types=1);

use Cortex\JsonSchema\Schema;
use Cortex\OpenApi\Objects\Server;
use Cortex\OpenApi\Objects\PathItem;
use Cortex\OpenApi\Objects\Operation;
use Cortex\OpenApi\Objects\Parameter;

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
    expect(PathItem::ref('#/components/pathItems/UserById')->toArray())->toBe([
        '$ref' => '#/components/pathItems/UserById',
    ]);
});
