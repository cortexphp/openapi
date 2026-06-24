<?php

declare(strict_types=1);

use Cortex\OpenApi\OpenApi;
use Cortex\JsonSchema\Schema;
use Cortex\OpenApi\Objects\Info;
use Cortex\OpenApi\Objects\Components;

covers(OpenApi::class);

it('rejects a components key containing invalid characters', function (): void {
    $openApi = OpenApi::create()
        ->info(Info::create('x', '1.0.0'))
        ->components(
            Components::create()->schema('Invalid Key!', Schema::object()),
        );

    expect($openApi)->toFailOpenApiValidationAt(
        '/components/schemas',
        'The string should match pattern: ^[a-zA-Z0-9._-]+$',
    );
});
