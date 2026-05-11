<?php

declare(strict_types=1);

use Cortex\OpenApi\Enums\In;
use Cortex\JsonSchema\Schema;
use Cortex\OpenApi\Objects\Link;
use Cortex\OpenApi\Objects\Header;
use Cortex\OpenApi\Objects\Example;
use Cortex\OpenApi\Objects\Callback;
use Cortex\OpenApi\Objects\PathItem;
use Cortex\OpenApi\Objects\Response;
use Cortex\OpenApi\Objects\MediaType;
use Cortex\OpenApi\Objects\Operation;
use Cortex\OpenApi\Objects\Parameter;
use Cortex\OpenApi\Objects\Reference;
use Cortex\OpenApi\Objects\Components;
use Cortex\OpenApi\Objects\RequestBody;
use Cortex\OpenApi\Objects\SecurityScheme;

covers(Components::class);

it('emits nothing by default', function (): void {
    expect(Components::create()->toArray())->toBe([]);
});

it('registers schemas', function (): void {
    $components = Components::create()
        ->schema('User', Schema::object()->properties(Schema::string('id')))
        ->schema('Error', [
            'type' => 'object',
        ]);

    expect($components->toArray())->toBe([
        'schemas' => [
            'User' => [
                'type' => 'object',
                'properties' => [
                    'id' => [
                        'type' => 'string',
                    ],
                ],
            ],
            'Error' => [
                'type' => 'object',
            ],
        ],
    ]);
});

it('registers every component bucket', function (): void {
    $components = Components::create()
        ->schema('User', Schema::object())
        ->response('NotFound', Response::notFound())
        ->parameter('PageSize', Parameter::query('size', Schema::integer()))
        ->example('UserExample', Example::create()->value([
            'id' => '1',
        ]))
        ->requestBody('CreateUser', RequestBody::create()->content(MediaType::json(Schema::string())))
        ->header('X-RateLimit', Header::create()->schema(Schema::integer()))
        ->securityScheme('ApiKey', SecurityScheme::apiKey('X-API-Key', In::Header))
        ->link('UserLink', Link::create()->operationId('users.show'))
        ->callback(
            'WebhookReceived',
            Callback::create()->expression('{$request.body#/url}', PathItem::create('/x')->operations(
                Operation::post(),
            )),
        )
        ->pathItem('UserById', PathItem::create('/users/{id}')->summary('User by id'));

    expect($components->toArray())->toBe([
        'schemas' => [
            'User' => [
                'type' => 'object',
            ],
        ],
        'responses' => [
            'NotFound' => [
                'description' => 'Not Found',
            ],
        ],
        'parameters' => [
            'PageSize' => [
                'name' => 'size',
                'in' => 'query',
                'schema' => [
                    'type' => 'integer',
                ],
            ],
        ],
        'examples' => [
            'UserExample' => [
                'value' => [
                    'id' => '1',
                ],
            ],
        ],
        'requestBodies' => [
            'CreateUser' => [
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'string',
                        ],
                    ],
                ],
            ],
        ],
        'headers' => [
            'X-RateLimit' => [
                'schema' => [
                    'type' => 'integer',
                ],
            ],
        ],
        'securitySchemes' => [
            'ApiKey' => [
                'type' => 'apiKey',
                'name' => 'X-API-Key',
                'in' => 'header',
            ],
        ],
        'links' => [
            'UserLink' => [
                'operationId' => 'users.show',
            ],
        ],
        'callbacks' => [
            'WebhookReceived' => [
                '{$request.body#/url}' => [
                    'post' => [],
                ],
            ],
        ],
        'pathItems' => [
            'UserById' => [
                'summary' => 'User by id',
            ],
        ],
    ]);
});

it('accepts a Reference in every bucket', function (): void {
    $components = Components::create()
        ->schema('User', Reference::schema('UserV1'))
        ->response('NotFound', Reference::response('NotFoundV1'));

    expect($components->toArray())->toBe([
        'schemas' => [
            'User' => [
                '$ref' => '#/components/schemas/UserV1',
            ],
        ],
        'responses' => [
            'NotFound' => [
                '$ref' => '#/components/responses/NotFoundV1',
            ],
        ],
    ]);
});
