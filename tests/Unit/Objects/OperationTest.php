<?php

declare(strict_types=1);

use Cortex\JsonSchema\Schema;
use Cortex\OpenApi\Objects\Server;
use Cortex\OpenApi\Enums\HttpMethod;
use Cortex\OpenApi\Objects\Callback;
use Cortex\OpenApi\Objects\PathItem;
use Cortex\OpenApi\Objects\Response;
use Cortex\OpenApi\Objects\MediaType;
use Cortex\OpenApi\Objects\Operation;
use Cortex\OpenApi\Objects\Parameter;
use Cortex\OpenApi\Objects\Reference;
use Cortex\OpenApi\Objects\RequestBody;
use Cortex\OpenApi\Objects\ExternalDocs;
use Cortex\OpenApi\Objects\SecurityRequirement;

covers(Operation::class);

it('knows its HTTP method', function (): void {
    expect(Operation::get()->getMethod())->toBe(HttpMethod::Get);
    expect(Operation::post()->getMethod())->toBe(HttpMethod::Post);
    expect(Operation::put()->getMethod())->toBe(HttpMethod::Put);
    expect(Operation::patch()->getMethod())->toBe(HttpMethod::Patch);
    expect(Operation::delete()->getMethod())->toBe(HttpMethod::Delete);
    expect(Operation::options()->getMethod())->toBe(HttpMethod::Options);
    expect(Operation::head()->getMethod())->toBe(HttpMethod::Head);
    expect(Operation::trace()->getMethod())->toBe(HttpMethod::Trace);
});

it('emits nothing by default', function (): void {
    expect(Operation::get()->toArray())->toBe([]);
});

it('emits tags/summary/description/operationId/deprecated', function (): void {
    $operation = Operation::post()
        ->tags('Users', 'Admin')
        ->summary('Create user')
        ->description('Creates a new user')
        ->operationId('users.create')
        ->deprecated(true);

    expect($operation->toArray())->toBe([
        'tags' => ['Users', 'Admin'],
        'summary' => 'Create user',
        'description' => 'Creates a new user',
        'operationId' => 'users.create',
        'deprecated' => true,
    ]);
});

it('emits parameters, requestBody, and responses', function (): void {
    $operation = Operation::post()
        ->parameters(Parameter::query('dry_run', Schema::boolean()))
        ->requestBody(RequestBody::create()->content(MediaType::json(Schema::string())))
        ->responses(
            Response::created()->content(MediaType::json(Schema::string())),
            Response::badRequest(),
        );

    expect($operation->toArray())->toBe([
        'parameters' => [
            [
                'name' => 'dry_run',
                'in' => 'query',
                'schema' => [
                    'type' => 'boolean',
                ],
            ],
        ],
        'requestBody' => [
            'content' => [
                'application/json' => [
                    'schema' => [
                        'type' => 'string',
                    ],
                ],
            ],
        ],
        'responses' => [
            '201' => [
                'description' => 'Created',
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'string',
                        ],
                    ],
                ],
            ],
            '400' => [
                'description' => 'Bad Request',
            ],
        ],
    ]);
});

it('emits externalDocs, servers, and security', function (): void {
    $operation = Operation::get()
        ->externalDocs(ExternalDocs::create('https://example.com'))
        ->security(SecurityRequirement::create('oauth2', ['read']));

    expect($operation->toArray())->toBe([
        'externalDocs' => [
            'url' => 'https://example.com',
        ],
        'security' => [[
            'oauth2' => ['read'],
        ]],
    ]);
});

it('deprecated() defaults to true', function (): void {
    expect(Operation::get()->deprecated()->toArray())->toMatchArray([
        'deprecated' => true,
    ]);
});

it('emits servers when set', function (): void {
    $operation = Operation::get()->servers(Server::create('https://api.example.com'));

    expect($operation->toArray())->toBe([
        'servers' => [[
            'url' => 'https://api.example.com',
        ]],
    ]);
});

it('responses() accepts only Response objects', function (): void {
    $operation = Operation::get()->responses(
        Response::ok(),
        Response::notFound(),
    );

    expect($operation->toArray()['responses'])->toHaveKeys(['200', '404']);
});

it('adds a response by explicit status key, accepting Response or Reference', function (): void {
    $operation = Operation::get()
        ->response('200', Response::ok())
        ->response('404', Reference::response('NotFound'));

    expect($operation->toArray()['responses'])->toBe([
        '200' => ['description' => 'OK'],
        '404' => ['$ref' => '#/components/responses/NotFound'],
    ]);
});

it('emits callbacks when set', function (): void {
    $operation = Operation::post()->callbacks([
        'onData' => Callback::create()->expression('{$url}', PathItem::create('/hook')),
    ]);

    expect($operation->toArray())->toHaveKey('callbacks');
});

it('adds callbacks one at a time with callback()', function (): void {
    $operation = Operation::post()
        ->callback('onData', Callback::create()->expression('{$url}', PathItem::create('/hook')))
        ->callback('onError', Callback::ref('#/components/callbacks/OnError'));

    $arr = $operation->toArray();
    expect($arr['callbacks'])->toHaveKey('onData');
    expect($arr['callbacks'])->toHaveKey('onError');
});
