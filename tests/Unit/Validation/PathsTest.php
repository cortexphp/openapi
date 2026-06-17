<?php

declare(strict_types=1);

use Cortex\OpenApi\OpenApi;
use Cortex\OpenApi\Objects\Info;
use Cortex\OpenApi\Objects\PathItem;
use Cortex\OpenApi\Objects\Response;
use Cortex\OpenApi\Objects\Operation;

covers(OpenApi::class);

it('rejects a path key not starting with a forward slash', function (): void {
    $openApi = OpenApi::create()
        ->info(Info::create('x', '1.0.0'))
        ->path('noslash', PathItem::create('noslash')->operations(
            Operation::get()->responses(Response::ok()),
        ));

    expect($openApi)->toFailOpenApiValidationAt(
        '/paths',
        'Unevaluated object properties not allowed: noslash',
    );
});
