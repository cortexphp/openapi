<?php

declare(strict_types=1);

use Cortex\JsonSchema\Schema;
use Cortex\OpenApi\Enums\Style;
use Cortex\OpenApi\Objects\Header;
use Cortex\OpenApi\Objects\Encoding;
use Cortex\OpenApi\Objects\Reference;

covers(Encoding::class);

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
            'X-Custom' => [
                'schema' => [
                    'type' => 'string',
                ],
            ],
        ],
        'style' => 'form',
        'explode' => false,
        'allowReserved' => true,
    ]);
});

it('explode() defaults to true', function (): void {
    expect(Encoding::create()->explode()->toArray())->toBe([
        'explode' => true,
    ]);
});

it('allowReserved() defaults to true', function (): void {
    expect(Encoding::create()->allowReserved()->toArray())->toBe([
        'allowReserved' => true,
    ]);
});

it('adds headers one at a time with header()', function (): void {
    $encoding = Encoding::create()
        ->header('X-Custom', Header::create()->schema(Schema::string()))
        ->header('X-Ref', Reference::header('MyHeader'));

    expect($encoding->toArray()['headers'])->toHaveKeys(['X-Custom', 'X-Ref']);
});

it('accepts a Style enum for style()', function (): void {
    $encoding = Encoding::create()->style(Style::Form);

    expect($encoding->toArray())->toBe([
        'style' => 'form',
    ]);
});
