<?php

declare(strict_types=1);

use Cortex\OpenApi\Enums\In;
use Cortex\OpenApi\Objects\OAuthFlow;
use Cortex\OpenApi\Objects\OAuthFlows;
use Cortex\OpenApi\Objects\SecurityScheme;

it('builds an apiKey scheme', function (): void {
    $securityScheme = SecurityScheme::apiKey('X-API-Key', In::Header)
        ->description('API key auth');

    expect($securityScheme->toArray())->toBe([
        'type' => 'apiKey',
        'description' => 'API key auth',
        'name' => 'X-API-Key',
        'in' => 'header',
    ]);
});

it('builds an http scheme', function (): void {
    $securityScheme = SecurityScheme::http('bearer')->bearerFormat('JWT');

    expect($securityScheme->toArray())->toBe([
        'type' => 'http',
        'scheme' => 'bearer',
        'bearerFormat' => 'JWT',
    ]);
});

it('builds an oauth2 scheme with flows', function (): void {
    $securityScheme = SecurityScheme::oauth2(
        OAuthFlows::create()->authorizationCode(
            OAuthFlow::create()
                ->authorizationUrl('https://example.com/authorize')
                ->tokenUrl('https://example.com/token')
                ->scopes([
                    'read' => 'Read access',
                ]),
        ),
    );

    expect($securityScheme->toArray())->toBe([
        'type' => 'oauth2',
        'flows' => [
            'authorizationCode' => [
                'authorizationUrl' => 'https://example.com/authorize',
                'tokenUrl' => 'https://example.com/token',
                'scopes' => [
                    'read' => 'Read access',
                ],
            ],
        ],
    ]);
});

it('builds an openIdConnect scheme', function (): void {
    $securityScheme = SecurityScheme::openIdConnect('https://example.com/.well-known/openid-configuration');

    expect($securityScheme->toArray())->toBe([
        'type' => 'openIdConnect',
        'openIdConnectUrl' => 'https://example.com/.well-known/openid-configuration',
    ]);
});

it('builds a mutualTls scheme', function (): void {
    expect(SecurityScheme::mutualTls()->toArray())->toBe([
        'type' => 'mutualTLS',
    ]);
});

it('supports ref() shortcut', function (): void {
    expect(SecurityScheme::ref('#/components/securitySchemes/OAuth2')->toArray())->toBe([
        '$ref' => '#/components/securitySchemes/OAuth2',
    ]);
});
