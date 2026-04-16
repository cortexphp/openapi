<?php

declare(strict_types=1);

use Cortex\JsonSchema\Schema;
use Cortex\OpenApi\Objects\MediaType;
use Cortex\OpenApi\Objects\RequestBody;

covers(RequestBody::class);

it('builds a required JSON request body', function (): void {
    $requestBody = RequestBody::create()
        ->description('Create user payload')
        ->required(true)
        ->content(MediaType::json(Schema::object()->properties(Schema::string('name'))));

    expect($requestBody->toArray())->toBe([
        'description' => 'Create user payload',
        'required' => true,
        'content' => [
            'application/json' => [
                'schema' => [
                    'type' => 'object',
                    'properties' => [
                        'name' => [
                            'type' => 'string',
                        ],
                    ],
                ],
            ],
        ],
    ]);
});

it('combines multiple media types', function (): void {
    $requestBody = RequestBody::create()
        ->content(
            MediaType::json(Schema::string()),
            MediaType::xml(Schema::string()),
        );

    expect($requestBody->toArray())->toBe([
        'content' => [
            'application/json' => [
                'schema' => [
                    'type' => 'string',
                ],
            ],
            'application/xml' => [
                'schema' => [
                    'type' => 'string',
                ],
            ],
        ],
    ]);
});

it('supports ref() shortcut', function (): void {
    expect(RequestBody::ref('#/components/requestBodies/Create')->toArray())->toBe([
        '$ref' => '#/components/requestBodies/Create',
    ]);
});

it('required() defaults to true', function (): void {
    $requestBody = RequestBody::create()
        ->required()
        ->content(MediaType::json(Schema::string()));

    expect($requestBody->toArray())->toMatchArray(['required' => true]);
});
