<?php

declare(strict_types=1);

use Cortex\JsonSchema\Schema;
use Cortex\OpenApi\Objects\Link;
use Cortex\OpenApi\Objects\Header;
use Cortex\OpenApi\Objects\MediaType;
use Cortex\OpenApi\Objects\Response;

it('named 200 response uses "OK" description by default', function (): void {
    expect(Response::ok()->getStatusCode())->toBe('200');
    expect(Response::ok()->toArray())->toBe(['description' => 'OK']);
});

it('named 404 response', function (): void {
    $res = Response::notFound();

    expect($res->getStatusCode())->toBe('404');
    expect($res->toArray())->toBe(['description' => 'Not Found']);
});

it('arbitrary status via status()', function (): void {
    $res = Response::status(418)->description("I'm a teapot");

    expect($res->getStatusCode())->toBe('418');
    expect($res->toArray())->toBe(['description' => "I'm a teapot"]);
});

it('default response uses the default key', function (): void {
    $res = Response::default()->description('Unexpected error');

    expect($res->getStatusCode())->toBe('default');
    expect($res->toArray())->toBe(['description' => 'Unexpected error']);
});

it('emits content, headers, and links', function (): void {
    $res = Response::ok()
        ->description('OK')
        ->headers([
            'X-RateLimit' => Header::create()->schema(Schema::integer()),
        ])
        ->content(MediaType::json(Schema::string()))
        ->links([
            'self' => Link::create()->operationId('users.show'),
        ]);

    expect($res->toArray())->toBe([
        'description' => 'OK',
        'headers' => [
            'X-RateLimit' => ['schema' => ['type' => 'integer']],
        ],
        'content' => [
            'application/json' => ['schema' => ['type' => 'string']],
        ],
        'links' => [
            'self' => ['operationId' => 'users.show'],
        ],
    ]);
});

it('supports ref() shortcut', function (): void {
    expect(Response::ref('#/components/responses/Unauthorized')->toArray())->toBe([
        '$ref' => '#/components/responses/Unauthorized',
    ]);
});
