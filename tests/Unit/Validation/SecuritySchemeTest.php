<?php

declare(strict_types=1);

use Cortex\OpenApi\OpenApi;
use Cortex\OpenApi\Enums\In;
use Cortex\OpenApi\Objects\Info;
use Cortex\OpenApi\Objects\OAuthFlow;
use Cortex\OpenApi\Objects\Components;
use Cortex\OpenApi\Objects\OAuthFlows;
use Cortex\OpenApi\Objects\SecurityScheme;

covers(OpenApi::class);

it('accepts all security scheme types in components', function (): void {
    $openApi = OpenApi::create()
        ->info(Info::create('x', '1.0.0'))
        ->components(
            Components::create()
                ->securityScheme('apiKey', SecurityScheme::apiKey('X-API-Key', In::Header))
                ->securityScheme('bearer', SecurityScheme::http('bearer'))
                ->securityScheme('mutualTls', SecurityScheme::mutualTls())
                ->securityScheme('oauth2', SecurityScheme::oauth2(
                    OAuthFlows::create()->authorizationCode(
                        OAuthFlow::create()
                            ->authorizationUrl('https://example.com/oauth/authorize')
                            ->tokenUrl('https://example.com/oauth/token')
                            ->scopes([
                                'read' => 'Read access',
                            ]),
                    ),
                ))
                ->securityScheme(
                    'oidc',
                    SecurityScheme::openIdConnect('https://example.com/.well-known/openid-configuration'),
                ),
        );

    expect($openApi)->toPassOpenApiValidation();
});

it('rejects an oauth2 security scheme without flows', function (): void {
    $openApi = OpenApi::create()
        ->info(Info::create('x', '1.0.0'))
        ->components(
            Components::create()
                ->securityScheme('oauth2', SecurityScheme::oauth2()),
        );

    expect($openApi)->toFailOpenApiValidationAt(
        '/components/securitySchemes/oauth2',
        'The required properties (flows) are missing',
    );
});
