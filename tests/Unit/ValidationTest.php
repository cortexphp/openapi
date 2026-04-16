<?php

declare(strict_types=1);

use Cortex\OpenApi\OpenApi;
use Cortex\JsonSchema\Schema;
use Cortex\OpenApi\Objects\Tag;
use Cortex\OpenApi\Objects\Info;
use Cortex\OpenApi\Objects\PathItem;
use Cortex\OpenApi\Objects\Response;
use Cortex\OpenApi\Objects\MediaType;
use Cortex\OpenApi\Objects\Operation;
use Cortex\OpenApi\Objects\Components;
use Cortex\OpenApi\Enums\OpenApiVersion;
use Cortex\OpenApi\Exceptions\ValidationException;

covers(OpenApi::class);

it('accepts a minimal valid 3.1.0 document', function (): void {
    $openApi = OpenApi::create()
        ->info(Info::create()->title('x')->version('1.0.0'))
        ->paths(
            PathItem::create('/ping')->operations(
                Operation::get()->responses(Response::ok()),
            ),
        );

    $openApi->validate();
})->throwsNoExceptions();

it('accepts a minimal valid 3.1.1 document', function (): void {
    $openApi = OpenApi::create(OpenApiVersion::V3_1_1)
        ->info(Info::create()->title('x')->version('1.0.0'))
        ->paths(
            PathItem::create('/ping')->operations(
                Operation::get()->responses(Response::ok()),
            ),
        );

    $openApi->validate();
})->throwsNoExceptions();

it('accepts a document with components, tags, and schemas', function (): void {
    $openApi = OpenApi::create()
        ->info(Info::create()->title('Example')->version('1.0.0'))
        ->tags(Tag::create('Users'))
        ->components(Components::create()->schema('User', Schema::object()->properties(Schema::string('id'))))
        ->paths(
            PathItem::create('/users')->operations(
                Operation::get()->tags('Users')->responses(
                    Response::ok()->content(MediaType::json(Schema::string())),
                ),
            ),
        );

    $openApi->validate();
})->throwsNoExceptions();

it('rejects a document missing info', function (): void {
    $openApi = OpenApi::create();

    expect(fn(): mixed => $openApi->validate())->toThrow(ValidationException::class);
});

it('rejects a document with a bad response status key', function (): void {
    // 99 is not a valid HTTP status code, nor 'default'
    $openApi = OpenApi::create()
        ->info(Info::create()->title('x')->version('1.0.0'))
        ->paths(
            PathItem::create('/ping')->operations(
                Operation::get()->responses(Response::status(99)),
            ),
        );

    expect(fn(): mixed => $openApi->validate())->toThrow(ValidationException::class);
});
