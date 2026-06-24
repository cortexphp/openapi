<?php

declare(strict_types=1);

use Cortex\OpenApi\OpenApi;
use Cortex\OpenApi\Objects\Info;
use Cortex\OpenApi\Objects\Example;
use Cortex\OpenApi\Objects\Components;

covers(OpenApi::class);

it('rejects an example with both value and externalValue', function (): void {
    $openApi = OpenApi::create()
        ->info(Info::create('x', '1.0.0'))
        ->components(
            Components::create()
                ->example('Bad', Example::create()
                    ->value([
                        'id' => 1,
                    ])
                    ->externalValue('https://example.com/examples/user.json')),
        );

    expect($openApi)->toFailOpenApiValidationAt(
        '/components/examples/Bad',
        'The data must not match schema',
    );
});
