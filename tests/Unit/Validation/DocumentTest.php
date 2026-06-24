<?php

declare(strict_types=1);

use Cortex\OpenApi\OpenApi;
use Cortex\JsonSchema\Schema;
use Cortex\OpenApi\Objects\Tag;
use Cortex\OpenApi\Objects\Info;
use Cortex\OpenApi\Objects\PathItem;
use Cortex\OpenApi\Objects\Response;
use Cortex\OpenApi\Objects\MediaType;
use Cortex\OpenApi\Objects\Operation;
use Cortex\OpenApi\Objects\Components;
use Cortex\OpenApi\Enums\OpenApiVersion;

covers(OpenApi::class);

it('accepts a minimal valid 3.1.0 document', function (): void {
    $openApi = OpenApi::create()
        ->info(Info::create('x', '1.0.0'))
        ->paths(
            PathItem::create('/ping')->operations(
                Operation::get()->responses(Response::ok()),
            ),
        );

    expect($openApi)->toPassOpenApiValidation();
});

it('accepts a minimal valid 3.1.1 document', function (): void {
    $openApi = OpenApi::create(OpenApiVersion::V3_1_1)
        ->info(Info::create('x', '1.0.0'))
        ->paths(
            PathItem::create('/ping')->operations(
                Operation::get()->responses(Response::ok()),
            ),
        );

    expect($openApi)->toPassOpenApiValidation();
});

it('accepts a document with components, tags, and schemas', function (): void {
    $openApi = OpenApi::create()
        ->info(Info::create('Example', '1.0.0'))
        ->tags(Tag::create('Users'))
        ->components(Components::create()->schema('User', Schema::object()->properties(Schema::string('id'))))
        ->paths(
            PathItem::create('/users')->operations(
                Operation::get()->tags('Users')->responses(
                    Response::ok()->content(MediaType::json(Schema::string())),
                ),
            ),
        );

    expect($openApi)->toPassOpenApiValidation();
});

it('rejects a document missing info', function (): void {
    $openApi = OpenApi::create();

    expect($openApi)->toFailOpenApiValidationAt(
        '/',
        'The required properties (info) are missing',
    );
});

it('accepts a components-only document without paths or webhooks', function (): void {
    $openApi = OpenApi::create()
        ->info(Info::create('x', '1.0.0'))
        ->components(Components::create()->schema('User', Schema::object()));

    expect($openApi)->toPassOpenApiValidation();
});

it('accepts a webhooks-only document without paths or components', function (): void {
    $openApi = OpenApi::create()
        ->info(Info::create('x', '1.0.0'))
        ->webhooks([
            'user.created' => PathItem::create('/user.created')->operations(
                Operation::post()->responses(Response::ok()),
            ),
        ]);

    expect($openApi)->toPassOpenApiValidation();
});

it('rejects a document with info but no paths, components, or webhooks', function (): void {
    $openApi = OpenApi::create()
        ->info(Info::create('x', '1.0.0'));

    expect($openApi)->toFailOpenApiValidation([
        '/' => [
            'The required properties (paths) are missing',
            'The required properties (components) are missing',
            'The required properties (webhooks) are missing',
        ],
    ]);
});
