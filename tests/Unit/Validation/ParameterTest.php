<?php

declare(strict_types=1);

use Cortex\OpenApi\OpenApi;
use Cortex\JsonSchema\Schema;
use Cortex\OpenApi\Objects\Info;
use Cortex\OpenApi\Objects\PathItem;
use Cortex\OpenApi\Objects\Response;
use Cortex\OpenApi\Objects\Operation;
use Cortex\OpenApi\Objects\Parameter;

covers(OpenApi::class);

it('accepts all four parameter locations', function (): void {
    $openApi = OpenApi::create()
        ->info(Info::create('x', '1.0.0'))
        ->paths(
            PathItem::create('/items/{id}')->operations(
                Operation::get()
                    ->parameters(
                        Parameter::path('id', Schema::string()),
                        Parameter::query('filter', Schema::string()),
                        Parameter::header('X-Request-Id', Schema::string()),
                        Parameter::cookie('session', Schema::string()),
                    )
                    ->responses(Response::ok()),
            ),
        );

    expect($openApi)->toPassOpenApiValidation();
});

it('rejects a parameter with neither schema nor content', function (): void {
    $openApi = OpenApi::create()
        ->info(Info::create('x', '1.0.0'))
        ->paths(
            PathItem::create('/ping')->operations(
                Operation::get()
                    ->parameters(Parameter::query('q'))
                    ->responses(Response::ok()),
            ),
        );

    expect($openApi)->toFailOpenApiValidation([
        '/paths/~1ping/get/parameters/0' => [
            'The required properties (schema) are missing',
            'The required properties (content) are missing',
        ],
    ]);
});

it('rejects a path parameter where required is overridden to false', function (): void {
    $openApi = OpenApi::create()
        ->info(Info::create('x', '1.0.0'))
        ->paths(
            PathItem::create('/items/{id}')->operations(
                Operation::get()
                    ->parameters(Parameter::path('id', Schema::string())->required(false))
                    ->responses(Response::ok()),
            ),
        );

    expect($openApi)->toFailOpenApiValidationAt(
        '/paths/~1items~1%7Bid%7D/get/parameters/0/required',
        'The data must match the const value',
    );
});
