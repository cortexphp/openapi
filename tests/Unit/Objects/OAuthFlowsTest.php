<?php

declare(strict_types=1);

use Cortex\OpenApi\Objects\OAuthFlow;
use Cortex\OpenApi\Objects\OAuthFlows;

covers(OAuthFlows::class, OAuthFlow::class);

it('emits nothing by default', function (): void {
    expect(OAuthFlows::create()->toArray())->toBe([]);
});

it('emits all four flows when configured', function (): void {
    $oAuthFlows = OAuthFlows::create()
        ->implicit(OAuthFlow::create()
            ->authorizationUrl('https://example.com/authorize')
            ->scopes([
                'read' => 'Read',
            ]))
        ->password(OAuthFlow::create()
            ->tokenUrl('https://example.com/token')
            ->scopes([
                'read' => 'Read',
            ]))
        ->clientCredentials(OAuthFlow::create()
            ->tokenUrl('https://example.com/token')
            ->scopes([
                'write' => 'Write',
            ]))
        ->authorizationCode(OAuthFlow::create()
            ->authorizationUrl('https://example.com/authorize')
            ->tokenUrl('https://example.com/token')
            ->refreshUrl('https://example.com/refresh')
            ->scopes([
                'admin' => 'Admin',
            ]));

    expect($oAuthFlows->toArray())->toBe([
        'implicit' => [
            'authorizationUrl' => 'https://example.com/authorize',
            'scopes' => [
                'read' => 'Read',
            ],
        ],
        'password' => [
            'tokenUrl' => 'https://example.com/token',
            'scopes' => [
                'read' => 'Read',
            ],
        ],
        'clientCredentials' => [
            'tokenUrl' => 'https://example.com/token',
            'scopes' => [
                'write' => 'Write',
            ],
        ],
        'authorizationCode' => [
            'authorizationUrl' => 'https://example.com/authorize',
            'tokenUrl' => 'https://example.com/token',
            'refreshUrl' => 'https://example.com/refresh',
            'scopes' => [
                'admin' => 'Admin',
            ],
        ],
    ]);
});
