<?php

declare(strict_types=1);

use Cortex\JsonSchema\Schema;
use Cortex\OpenApi\Objects\Link;
use Cortex\OpenApi\Objects\Header;
use Cortex\OpenApi\Objects\Response;
use Cortex\OpenApi\Objects\MediaType;

covers(Response::class);

it('named 200 response uses "OK" description by default', function (): void {
    expect(Response::ok()->getStatusCode())->toBe('200');
    expect(Response::ok()->toArray())->toBe([
        'description' => 'OK',
    ]);
});

it('named 404 response', function (): void {
    $response = Response::notFound();

    expect($response->getStatusCode())->toBe('404');
    expect($response->toArray())->toBe([
        'description' => 'Not Found',
    ]);
});

it('arbitrary status via status()', function (): void {
    $response = Response::status(418)->description("I'm a teapot");

    expect($response->getStatusCode())->toBe('418');
    expect($response->toArray())->toBe([
        'description' => "I'm a teapot",
    ]);
});

it('default response uses the default key', function (): void {
    $response = Response::default()->description('Unexpected error');

    expect($response->getStatusCode())->toBe('default');
    expect($response->toArray())->toBe([
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

it('supports ref() shortcut', function (): void {
    expect(Response::ref('#/components/responses/Unauthorized')->toArray())->toBe([
        '$ref' => '#/components/responses/Unauthorized',
    ]);
});
