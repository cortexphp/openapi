<?php

declare(strict_types=1);

use Cortex\OpenApi\Objects\Discriminator;

covers(Discriminator::class);

it('emits a propertyName-only discriminator', function (): void {
    expect(Discriminator::create('petType')->toArray())->toBe([
        'propertyName' => 'petType',
    ]);
});

it('emits a mapping', function (): void {
    $discriminator = Discriminator::create('petType')->mapping([
        'dog' => '#/components/schemas/Dog',
        'cat' => '#/components/schemas/Cat',
    ]);

    expect($discriminator->toArray())->toBe([
        'propertyName' => 'petType',
        'mapping' => [
            'dog' => '#/components/schemas/Dog',
            'cat' => '#/components/schemas/Cat',
        ],
    ]);
});
