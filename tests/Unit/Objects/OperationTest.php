<?php

declare(strict_types=1);

use Cortex\JsonSchema\Schema;
use Cortex\OpenApi\Enums\HttpMethod;
use Cortex\OpenApi\Objects\Parameter;
use Cortex\OpenApi\Objects\MediaType;
use Cortex\OpenApi\Objects\Response;
use Cortex\OpenApi\Objects\Operation;
use Cortex\OpenApi\Objects\RequestBody;
use Cortex\OpenApi\Objects\ExternalDocs;
use Cortex\OpenApi\Objects\SecurityRequirement;

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
    $op = Operation::post()
        ->tags('Users', 'Admin')
        ->summary('Create user')
        ->description('Creates a new user')
        ->operationId('users.create')
        ->deprecated(true);

    expect($op->toArray())->toBe([
        'tags' => ['Users', 'Admin'],
        'summary' => 'Create user',
        'description' => 'Creates a new user',
        'operationId' => 'users.create',
        'deprecated' => true,
    ]);
});

it('emits parameters, requestBody, and responses', function (): void {
    $op = Operation::post()
        ->parameters(Parameter::query('dry_run', Schema::boolean()))
        ->requestBody(RequestBody::create()->content(MediaType::json(Schema::string())))
        ->responses(
            Response::created()->content(MediaType::json(Schema::string())),
            Response::badRequest(),
        );

    expect($op->toArray())->toBe([
        'parameters' => [
            [
                'name' => 'dry_run',
                'in' => 'query',
                'schema' => ['type' => 'boolean'],
            ],
        ],
        'requestBody' => [
            'content' => [
                'application/json' => ['schema' => ['type' => 'string']],
            ],
        ],
        'responses' => [
            '201' => [
                'description' => 'Created',
                'content' => [
                    'application/json' => ['schema' => ['type' => 'string']],
                ],
            ],
            '400' => ['description' => 'Bad Request'],
        ],
    ]);
});

it('emits externalDocs, servers, and security', function (): void {
    $op = Operation::get()
        ->externalDocs(ExternalDocs::create('https://example.com'))
        ->security(SecurityRequirement::create('oauth2', ['read']));

    expect($op->toArray())->toBe([
        'externalDocs' => ['url' => 'https://example.com'],
        'security' => [['oauth2' => ['read']]],
    ]);
});
