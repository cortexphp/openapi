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
