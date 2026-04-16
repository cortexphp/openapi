<?php

declare(strict_types=1);

use Cortex\OpenApi\Objects\SecurityRequirement;

covers(SecurityRequirement::class);

it('builds a scheme with scopes', function (): void {
    expect(SecurityRequirement::create('oauth2', ['read:users', 'write:users'])->toArray())->toBe([
        'oauth2' => ['read:users', 'write:users'],
    ]);
});

it('builds a scheme with no scopes', function (): void {
    expect(SecurityRequirement::create('apiKey')->toArray())->toBe([
        'apiKey' => [],
    ]);
});

it('builds an empty requirement (public access)', function (): void {
    expect(SecurityRequirement::public()->toArray())->toBe([]);
});
