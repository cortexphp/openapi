<?php

declare(strict_types=1);

use Cortex\OpenApi\Objects\Reference;
use Cortex\OpenApi\Contracts\Serializable;

it('builds a basic $ref array', function (): void {
    $ref = Reference::to('#/components/schemas/User');

    expect($ref)->toBeInstanceOf(Serializable::class);
    expect($ref->toArray())->toBe([
        '$ref' => '#/components/schemas/User',
    ]);
});

it('includes summary and description when provided', function (): void {
    $ref = Reference::to(
        '#/components/responses/NotFound',
        summary: 'Not found',
        description: 'The resource was not found',
    );

    expect($ref->toArray())->toBe([
        '$ref' => '#/components/responses/NotFound',
        'summary' => 'Not found',
        'description' => 'The resource was not found',
    ]);
});

it('omits summary and description when null', function (): void {
    $ref = Reference::to('#/components/schemas/User', summary: null, description: null);

    expect($ref->toArray())->toBe([
        '$ref' => '#/components/schemas/User',
    ]);
});

it('rejects an empty pointer', function (): void {
    Reference::to('');
})->throws(InvalidArgumentException::class);
