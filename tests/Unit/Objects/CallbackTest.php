<?php

declare(strict_types=1);

use Cortex\OpenApi\Objects\Callback;
use Cortex\OpenApi\Objects\PathItem;
use Cortex\OpenApi\Objects\Operation;

covers(Callback::class);

it('emits a map of expression to PathItem', function (): void {
    $callback = Callback::create()->expression(
        '{$request.body#/callbackUrl}',
        PathItem::create('/dummy')->operations(
            Operation::post()->operationId('callback.receive'),
        ),
    );

    expect($callback->toArray())->toBe([
        '{$request.body#/callbackUrl}' => [
            'post' => [
                'operationId' => 'callback.receive',
            ],
        ],
    ]);
});

it('supports multiple expressions', function (): void {
    $callback = Callback::create()
        ->expression('a', PathItem::create('/a'))
        ->expression('b', PathItem::create('/b')->summary('B'));

    expect($callback->toArray())->toBe([
        'a' => [],
        'b' => [
            'summary' => 'B',
        ],
    ]);
});

it('supports ref() shortcut', function (): void {
    expect(Callback::ref('Webhook')->toArray())->toBe([
        '$ref' => '#/components/callbacks/Webhook',
    ]);
});
