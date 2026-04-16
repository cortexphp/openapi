<?php

declare(strict_types=1);

use Cortex\OpenApi\Objects\Reference;
use Cortex\OpenApi\Contracts\Serializable;

covers(Reference::class);

it('builds a basic $ref array', function (): void {
    $reference = Reference::to('#/components/schemas/User');

    expect($reference)->toBeInstanceOf(Serializable::class);
    expect($reference->toArray())->toBe([
        '$ref' => '#/components/schemas/User',
    ]);
});

it('includes summary and description when provided', function (): void {
    $reference = Reference::to(
        '#/components/responses/NotFound',
        summary: 'Not found',
        description: 'The resource was not found',
    );

    expect($reference->toArray())->toBe([
        '$ref' => '#/components/responses/NotFound',
        'summary' => 'Not found',
        'description' => 'The resource was not found',
    ]);
});

it('omits summary and description when null', function (): void {
    $reference = Reference::to('#/components/schemas/User');

    expect($reference->toArray())->toBe([
        '$ref' => '#/components/schemas/User',
    ]);
});

it('rejects an empty pointer', function (): void {
    Reference::to('');
})->throws(InvalidArgumentException::class);

it('provides typed shortcuts for every component bucket', function (): void {
    expect(Reference::schema('User')->toArray())->toBe([
        '$ref' => '#/components/schemas/User',
    ]);

    expect(Reference::response('NotFound')->toArray())->toBe([
        '$ref' => '#/components/responses/NotFound',
    ]);

    expect(Reference::parameter('PetId')->toArray())->toBe([
        '$ref' => '#/components/parameters/PetId',
    ]);

    expect(Reference::requestBody('CreateUser')->toArray())->toBe([
        '$ref' => '#/components/requestBodies/CreateUser',
    ]);

    expect(Reference::header('RateLimit')->toArray())->toBe([
        '$ref' => '#/components/headers/RateLimit',
    ]);

    expect(Reference::example('Sample')->toArray())->toBe([
        '$ref' => '#/components/examples/Sample',
    ]);

    expect(Reference::link('NextPage')->toArray())->toBe([
        '$ref' => '#/components/links/NextPage',
    ]);

    expect(Reference::callback('OnCreate')->toArray())->toBe([
        '$ref' => '#/components/callbacks/OnCreate',
    ]);

    expect(Reference::securityScheme('BearerAuth')->toArray())->toBe([
        '$ref' => '#/components/securitySchemes/BearerAuth',
    ]);

    expect(Reference::pathItem('UserById')->toArray())->toBe([
        '$ref' => '#/components/pathItems/UserById',
    ]);
});
