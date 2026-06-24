<?php

declare(strict_types=1);

use Cortex\OpenApi\OpenApi;
use Cortex\JsonSchema\Schema;
use Cortex\OpenApi\Objects\Info;
use Cortex\OpenApi\Objects\Link;
use Cortex\OpenApi\Objects\Header;
use Cortex\OpenApi\Objects\PathItem;
use Cortex\OpenApi\Objects\Response;
use Cortex\OpenApi\Objects\Operation;

covers(OpenApi::class);

it('rejects a document with a bad response status key', function (): void {
    // 99 is not a valid HTTP status code, nor 'default'
    $openApi = OpenApi::create()
        ->info(Info::create('x', '1.0.0'))
        ->paths(
            PathItem::create('/ping')->operations(
                Operation::get()->responses(Response::status(99)),
            ),
        );

    expect($openApi)->toFailOpenApiValidationAt(
        '/paths/~1ping/get/responses',
        'Unevaluated object properties not allowed: 99',
    );
});

it('accepts wildcard and default response keys', function (): void {
    $openApi = OpenApi::create()
        ->info(Info::create('x', '1.0.0'))
        ->paths(
            PathItem::create('/ping')->operations(
                Operation::get()->responses(
                    Response::ok(),
                    Response::default()->description('Unexpected error'),
                    Response::status('4XX')->description('Client error'),
                ),
            ),
        );

    expect($openApi)->toPassOpenApiValidation();
});

it('rejects a response object missing its description', function (): void {
    // 503 has no default description, so the response serialises to {} which fails required:[description]
    $openApi = OpenApi::create()
        ->info(Info::create('x', '1.0.0'))
        ->paths(
            PathItem::create('/ping')->operations(
                Operation::get()->responses(Response::status(503)),
            ),
        );

    expect($openApi)->toFailOpenApiValidationAt(
        '/paths/~1ping/get/responses/503',
        'The data (array) must match the type: object',
    );
});

it('accepts response links and response headers', function (): void {
    $openApi = OpenApi::create()
        ->info(Info::create('x', '1.0.0'))
        ->paths(
            PathItem::create('/users/{id}')->operations(
                Operation::get()->responses(
                    Response::ok()
                        ->header('X-Rate-Limit', Header::create()->schema(Schema::integer()))
                        ->link('GetUser', Link::create()->operationId('getUser')),
                ),
            ),
        );

    expect($openApi)->toPassOpenApiValidation();
});

it('rejects a link with neither operationRef nor operationId', function (): void {
    $openApi = OpenApi::create()
        ->info(Info::create('x', '1.0.0'))
        ->paths(
            PathItem::create('/ping')->operations(
                Operation::get()->responses(
                    Response::ok()->link('Empty', Link::create()),
                ),
            ),
        );

    expect($openApi)->toFailOpenApiValidationAt(
        '/paths/~1ping/get/responses/200/links/Empty',
        'The data (array) must match the type: object',
    );
});
