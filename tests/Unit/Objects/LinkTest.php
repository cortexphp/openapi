<?php

declare(strict_types=1);

use Cortex\OpenApi\Objects\Link;
use Cortex\OpenApi\Objects\Server;

covers(Link::class);

it('emits nothing when empty', function (): void {
    expect(Link::create()->toArray())->toBe([]);
});

it('emits operationId-based link', function (): void {
    $link = Link::create()
        ->operationId('users.show')
        ->parameters([
            'id' => '$response.body#/id',
        ])
        ->description('Fetch the created user')
        ->server(Server::create('https://api.example.com'));

    expect($link->toArray())->toBe([
        'operationId' => 'users.show',
        'parameters' => [
            'id' => '$response.body#/id',
        ],
        'description' => 'Fetch the created user',
        'server' => [
            'url' => 'https://api.example.com',
        ],
    ]);
});

it('supports operationRef and requestBody', function (): void {
    $link = Link::create()
        ->operationRef('#/paths/~1users~1{id}/get')
        ->requestBody([
            'body' => 'value',
        ]);

    expect($link->toArray())->toBe([
        'operationRef' => '#/paths/~1users~1{id}/get',
        'requestBody' => [
            'body' => 'value',
        ],
    ]);
});

it('supports ref() shortcut', function (): void {
    expect(Link::ref('#/components/links/Foo')->toArray())->toBe([
        '$ref' => '#/components/links/Foo',
    ]);
});
