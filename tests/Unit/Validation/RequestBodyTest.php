<?php

declare(strict_types=1);

use Cortex\OpenApi\OpenApi;
use Cortex\JsonSchema\Schema;
use Cortex\OpenApi\Objects\Info;
use Cortex\OpenApi\Objects\PathItem;
use Cortex\OpenApi\Objects\Response;
use Cortex\OpenApi\Objects\Operation;
use Cortex\OpenApi\Objects\Components;
use Cortex\OpenApi\Objects\RequestBody;

covers(OpenApi::class);

it('accepts a POST operation with a requestBody', function (): void {
    $openApi = OpenApi::create()
        ->info(Info::create('x', '1.0.0'))
        ->paths(
            PathItem::create('/items')->operations(
                Operation::post()
                    ->requestBody(RequestBody::create()->json(Schema::object()))
                    ->responses(Response::created()),
            ),
        );

    expect($openApi)->toPassOpenApiValidation();
});

it('rejects a requestBody with no content', function (): void {
    // An inline requestBody that serialises to {} is dropped by buildArray in Operation,
    // so we register it in components.requestBodies where the entry is always preserved.
    $openApi = OpenApi::create()
        ->info(Info::create('x', '1.0.0'))
        ->components(
            Components::create()->requestBody('Body', RequestBody::create()),
        );

    expect($openApi)->toFailOpenApiValidationAt(
        '/components/requestBodies/Body',
        'The data (array) must match the type: object',
    );
});
