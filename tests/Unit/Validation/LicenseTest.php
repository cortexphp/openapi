<?php

declare(strict_types=1);

use Cortex\OpenApi\OpenApi;
use Cortex\JsonSchema\Schema;
use Cortex\OpenApi\Objects\Info;
use Cortex\OpenApi\Objects\License;
use Cortex\OpenApi\Objects\Components;

covers(OpenApi::class);

it('rejects a license with both identifier and url', function (): void {
    $openApi = OpenApi::create()
        ->info(
            Info::create('x', '1.0.0')
                ->license(License::create('MIT')
                    ->identifier('MIT')
                    ->url('https://spdx.org/licenses/MIT.html')),
        )
        ->components(Components::create()->schema('Empty', Schema::object()));

    expect($openApi)->toFailOpenApiValidationAt(
        '/info/license',
        'The data must not match schema',
    );
});
