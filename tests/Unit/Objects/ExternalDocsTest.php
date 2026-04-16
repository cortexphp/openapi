<?php

declare(strict_types=1);

use Cortex\OpenApi\Objects\ExternalDocs;

it('emits url and optional description', function (): void {
    expect(ExternalDocs::create('https://example.com/docs')->toArray())->toBe([
        'url' => 'https://example.com/docs',
    ]);

    expect(
        ExternalDocs::create('https://example.com/docs')->description('More info')->toArray(),
    )->toBe([
        'url' => 'https://example.com/docs',
        'description' => 'More info',
    ]);
});
