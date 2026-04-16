<?php

declare(strict_types=1);

use Cortex\JsonSchema\Schema;
use Cortex\OpenApi\Objects\Header;
use Cortex\OpenApi\Objects\Encoding;

it('emits only the contentType by default', function (): void {
    expect(Encoding::create()->contentType('image/png')->toArray())->toBe([
        'contentType' => 'image/png',
    ]);
});

it('emits headers and style flags', function (): void {
    $encoding = Encoding::create()
        ->contentType('application/xml')
        ->style('form')
        ->explode(false)
        ->allowReserved(true)
        ->headers([
            'X-Custom' => Header::create()->schema(Schema::string()),
        ]);

    expect($encoding->toArray())->toBe([
        'contentType' => 'application/xml',
        'headers' => [
            'X-Custom' => ['schema' => ['type' => 'string']],
        ],
        'style' => 'form',
        'explode' => false,
        'allowReserved' => true,
    ]);
});
