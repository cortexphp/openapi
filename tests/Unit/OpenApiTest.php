<?php

declare(strict_types=1);

use Cortex\OpenApi\OpenApi;
use Cortex\JsonSchema\Schema;
use Cortex\OpenApi\Objects\Tag;
use Cortex\OpenApi\Objects\Info;
use Cortex\OpenApi\Objects\Server;
use Cortex\OpenApi\Objects\PathItem;
use Cortex\OpenApi\Objects\Response;
use Cortex\OpenApi\Objects\Operation;
use Cortex\OpenApi\Objects\Components;
use Cortex\OpenApi\Enums\OpenApiVersion;
use Cortex\OpenApi\Objects\ExternalDocs;
use Cortex\OpenApi\Objects\SecurityRequirement;

covers(OpenApi::class);

it('defaults to OpenAPI 3.1.0', function (): void {
    $doc = OpenApi::create()->info(Info::create()->title('x')->version('1'));

    expect($doc->toArray())->toBe([
        'openapi' => '3.1.0',
        'info' => [
            'title' => 'x',
            'version' => '1',
        ],
    ]);

    $doc = OpenApi::create(OpenApiVersion::V3_1_1)->info(Info::create()->title('x')->version('1'));

    expect($doc->toArray())->toBe([
        'openapi' => '3.1.1',
        'info' => [
            'title' => 'x',
            'version' => '1',
        ],
    ]);
});

it('composes the whole document', function (): void {
    $openApi = OpenApi::create()
        ->info(Info::create()->title('Example API')->version('1.0.0'))
        ->servers(Server::create('https://api.example.com'))
        ->tags(Tag::create('Users'))
        ->externalDocs(ExternalDocs::create('https://example.com/docs'))
        ->security(SecurityRequirement::create('apiKey'))
        ->paths(
            PathItem::create('/users')->operations(
                Operation::get()->operationId('users.index')->responses(Response::ok()),
            ),
        )
        ->webhooks([
            'user.created' => PathItem::create('/user.created')->operations(
                Operation::post()->operationId('user.created'),
            ),
        ])
        ->components(Components::create()->schema('User', Schema::object()));

    expect($openApi->toArray())->toBe([
        'openapi' => '3.1.0',
        'info' => [
            'title' => 'Example API',
            'version' => '1.0.0',
        ],
        'servers' => [[
            'url' => 'https://api.example.com',
        ]],
        'paths' => [
            '/users' => [
                'get' => [
                    'operationId' => 'users.index',
                    'responses' => [
                        '200' => [
                            'description' => 'OK',
                        ],
                    ],
                ],
            ],
        ],
        'webhooks' => [
            'user.created' => [
                'post' => [
                    'operationId' => 'user.created',
                ],
            ],
        ],
        'components' => [
            'schemas' => [
                'User' => [
                    'type' => 'object',
                ],
            ],
        ],
        'security' => [[
            'apiKey' => [],
        ]],
        'tags' => [[
            'name' => 'Users',
        ]],
        'externalDocs' => [
            'url' => 'https://example.com/docs',
        ],
    ]);
});

it('toJson produces valid JSON', function (): void {
    $openApi = OpenApi::create()->info(Info::create()->title('x')->version('1'));
    $json = $openApi->toJson();

    expect(json_decode($json, true))->toBe($openApi->toArray());
});

it('toJson supports pretty printing', function (): void {
    $openApi = OpenApi::create()->info(Info::create()->title('x')->version('1'));

    expect($openApi->toJson(JSON_PRETTY_PRINT))->toContain("\n");
});

it('knows its OpenAPI version enum', function (): void {
    expect(OpenApi::create(OpenApiVersion::V3_1_0)->getVersion())->toBe(OpenApiVersion::V3_1_0);
    expect(OpenApi::create(OpenApiVersion::V3_1_1)->getVersion())->toBe(OpenApiVersion::V3_1_1);
});

it('supports vendor extensions at the root', function (): void {
    $openApi = OpenApi::create()
        ->info(Info::create()->title('x')->version('1'))
        ->x('x-internal', true);

    expect($openApi->toArray()['x-internal'])->toBe(true);
});

it('adds webhooks one at a time with webhook()', function (): void {
    $openApi = OpenApi::create()
        ->info(Info::create()->title('x')->version('1'))
        ->webhook('user.created', PathItem::create('/user.created')->operations(
            Operation::post()->operationId('user.created'),
        ))
        ->webhook('user.deleted', PathItem::create('/user.deleted')->operations(
            Operation::post()->operationId('user.deleted'),
        ));

    expect($openApi->toArray()['webhooks'])->toBe([
        'user.created' => [
            'post' => [
                'operationId' => 'user.created',
            ],
        ],
        'user.deleted' => [
            'post' => [
                'operationId' => 'user.deleted',
            ],
        ],
    ]);
});
