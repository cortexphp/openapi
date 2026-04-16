<?php

declare(strict_types=1);

use Cortex\OpenApi\Objects\OAuthFlow;

covers(OAuthFlow::class);

it('adds scopes one at a time with scope()', function (): void {
    $flow = OAuthFlow::create()
        ->authorizationUrl('https://example.com/authorize')
        ->tokenUrl('https://example.com/token')
        ->scope('read:users', 'Read users')
        ->scope('write:users', 'Write users');

    expect($flow->toArray())->toBe([
        'authorizationUrl' => 'https://example.com/authorize',
        'tokenUrl' => 'https://example.com/token',
        'scopes' => [
            'read:users' => 'Read users',
            'write:users' => 'Write users',
        ],
    ]);
});
