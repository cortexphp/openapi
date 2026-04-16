<?php

declare(strict_types=1);

use Cortex\JsonSchema\Schema;
use Cortex\OpenApi\Objects\Server;
use Cortex\OpenApi\Objects\Parameter;
use Cortex\OpenApi\Objects\Operation;
use Cortex\OpenApi\Objects\PathItem;

it('knows its own path', function (): void {
    expect(PathItem::create('/users')->getPath())->toBe('/users');
});

it('emits summary and description', function (): void {
    $path = PathItem::create('/users')
        ->summary('User collection')
        ->description('Operations on users');

    expect($path->toArray())->toBe([
        'summary' => 'User collection',
        'description' => 'Operations on users',
    ]);
});

it('emits operations keyed by HTTP method', function (): void {
    $path = PathItem::create('/users/{id}')
        ->operations(
            Operation::get()->operationId('users.show'),
            Operation::delete()->operationId('users.destroy'),
        );

    expect($path->toArray())->toBe([
        'get' => ['operationId' => 'users.show'],
        'delete' => ['operationId' => 'users.destroy'],
    ]);
});

it('emits shared parameters and servers', function (): void {
    $path = PathItem::create('/users/{id}')
        ->parameters(Parameter::path('id', Schema::string()))
        ->servers(Server::create('https://api.example.com'));

    expect($path->toArray())->toBe([
        'parameters' => [
            [
                'name' => 'id',
                'in' => 'path',
                'required' => true,
                'schema' => ['type' => 'string'],
            ],
        ],
        'servers' => [
            ['url' => 'https://api.example.com'],
        ],
    ]);
});

it('supports ref() shortcut', function (): void {
    expect(PathItem::ref('#/components/pathItems/UserById')->toArray())->toBe([
        '$ref' => '#/components/pathItems/UserById',
    ]);
});
