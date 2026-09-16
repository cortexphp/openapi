<?php

declare(strict_types=1);

use Cortex\JsonSchema\Schema;
use Cortex\OpenApi\Objects\Link;
use Cortex\OpenApi\Objects\Header;
use Cortex\OpenApi\Objects\Response;
use Cortex\OpenApi\Objects\MediaType;
use Cortex\OpenApi\Objects\Reference;

covers(Response::class);

it('named 200 response uses "OK" description by default', function (): void {
    expect(Response::ok()->getStatusCode())->toBe('200')
        ->and(Response::ok()
            ->toArray())
        ->toBe([
            'description' => 'OK',
        ]);
});

it('named 404 response', function (): void {
    $response = Response::notFound();

    expect($response->getStatusCode())->toBe('404')
        ->and($response->toArray())
        ->toBe([
            'description' => 'Not Found',
        ]);
});

it('arbitrary status via status()', function (): void {
    $response = Response::status(418)->description("I'm a teapot");

    expect($response->getStatusCode())->toBe('418')
        ->and($response->toArray())
        ->toBe([
            'description' => "I'm a teapot",
        ]);
});

it('default response uses the default key', function (): void {
    $response = Response::default()->description('Unexpected error');

    expect($response->getStatusCode())->toBe('default')
        ->and($response->toArray())
        ->toBe([
            'description' => 'Unexpected error',
        ]);
});

it('emits content, headers, and links', function (): void {
    $response = Response::ok()
        ->description('OK')
        ->headers([
            'X-RateLimit' => Header::create()->schema(Schema::integer()),
        ])
        ->content(MediaType::json(Schema::string()))
        ->links([
            'self' => Link::create()->operationId('users.show'),
        ]);

    expect($response->toArray())->toBe([
        'description' => 'OK',
        'headers' => [
            'X-RateLimit' => [
                'schema' => [
                    'type' => 'integer',
                ],
            ],
        ],
        'content' => [
            'application/json' => [
                'schema' => [
                    'type' => 'string',
                ],
            ],
        ],
        'links' => [
            'self' => [
                'operationId' => 'users.show',
            ],
        ],
    ]);
});

it('ref() serializes to the reference alone', function (): void {
    expect(Response::notFound()->ref('NotFound')->toArray())->toBe([
        '$ref' => '#/components/responses/NotFound',
    ]);
});

it('ref() keeps the status code so responses() can key it', function (): void {
    expect(Response::notFound()->ref('NotFound')->getStatusCode())->toBe('404');
});

it('ref() carries summary and description onto the reference', function (): void {
    expect(Response::unauthorized()->ref('Unauthorized', 'Auth failed', 'Token missing or expired')->toArray())
        ->toBe([
            '$ref' => '#/components/responses/Unauthorized',
            'summary' => 'Auth failed',
            'description' => 'Token missing or expired',
        ]);
});

it('ref() replaces any fields set on the response itself', function (): void {
    $response = Response::notFound()
        ->description('Locally described')
        ->json(Schema::object())
        ->ref('NotFound');

    expect($response->toArray())->toBe([
        '$ref' => '#/components/responses/NotFound',
    ]);
});

it('adds headers one at a time with header()', function (): void {
    $response = Response::ok()
        ->header('X-RateLimit-Limit', Header::create()->schema(Schema::integer()))
        ->header('X-RateLimit-Remaining', Header::create()->schema(Schema::integer()));

    expect($response->toArray()['headers'])->toHaveKeys(['X-RateLimit-Limit', 'X-RateLimit-Remaining']);
});

it('adds links one at a time with link()', function (): void {
    $response = Response::ok()
        ->link('self', Link::create()->operationId('users.show'))
        ->link('next', Reference::link('NextUser'));

    expect($response->toArray()['links'])->toHaveKeys(['self', 'next']);
});

it('json() sets application/json content in one call', function (): void {
    $response = Response::ok()->json(Schema::object()->properties(Schema::string('id')));

    expect($response->toArray())->toBe([
        'description' => 'OK',
        'content' => [
            'application/json' => [
                'schema' => [
                    'type' => 'object',
                    'properties' => [
                        'id' => [
                            'type' => 'string',
                        ],
                    ],
                ],
            ],
        ],
    ]);
});

it('json() accepts a Reference', function (): void {
    $response = Response::notFound()->json(Reference::schema('Error'));

    $content = expectArray($response->toArray()['content']);
    expect($content['application/json'])->toBe([
        'schema' => [
            '$ref' => '#/components/schemas/Error',
        ],
    ]);
});

it('json() with no schema emits an empty application/json key', function (): void {
    expect(Response::ok()->json()->toArray())->toHaveKey('content')
        ->and(Response::ok()->json()->toArray()['content'])
        ->toHaveKey('application/json');
});
