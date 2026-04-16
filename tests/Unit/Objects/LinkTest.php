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

it('inserts requestBody after parameters when parameters are present', function (): void {
    $link = Link::create()
        ->operationId('users.create')
        ->parameters(['id' => '$response.body#/id'])
        ->requestBody(['key' => 'value']);

    $array = $link->toArray();

    expect($array)->toHaveKey('requestBody');
    expect($array['requestBody'])->toBe(['key' => 'value']);
    $keys = array_keys($array);
    expect(array_search('requestBody', $keys))->toBeGreaterThan(array_search('parameters', $keys));
});

it('inserts requestBody before description when no parameters present', function (): void {
    $link = Link::create()
        ->operationId('users.show')
        ->requestBody(null)
        ->description('Fetch user');

    $array = $link->toArray();

    expect($array)->toHaveKey('requestBody');
    $keys = array_keys($array);
    expect(array_search('requestBody', $keys))->toBeLessThan(array_search('description', $keys));
});

it('inserts requestBody before server when no parameters or description', function (): void {
    $link = Link::create()
        ->operationId('users.show')
        ->requestBody(['body' => 'data'])
        ->server(Server::create('https://api.example.com'));

    $array = $link->toArray();

    expect($array)->toHaveKey('requestBody');
    $keys = array_keys($array);
    expect(array_search('requestBody', $keys))->toBeLessThan(array_search('server', $keys));
});

it('appends requestBody at end when no parameters, description, or server', function (): void {
    $link = Link::create()
        ->operationId('users.show')
        ->requestBody(['body' => 'data']);

    $array = $link->toArray();

    expect($array)->toHaveKey('requestBody');
    expect(array_key_last($array))->toBe('requestBody');
});
