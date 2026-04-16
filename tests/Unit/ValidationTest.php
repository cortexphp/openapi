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

it('validation exception message contains schema validation prefix', function (): void {
    $openApi = OpenApi::create();

    try {
        $openApi->validate();
        $this->fail('Expected ValidationException');
    } catch (ValidationException $validationException) {
        expect($validationException->getMessage())->toContain('OpenAPI document failed meta-schema validation:');
    }
});

it('validation exception message contains json-encoded errors', function (): void {
    $openApi = OpenApi::create();

    try {
        $openApi->validate();
    } catch (ValidationException $validationException) {
        // The message should contain both the prefix and JSON-encoded error details
        expect($validationException->getMessage())->toStartWith('OpenAPI document failed meta-schema validation: ');
        $jsonPart = substr(
            $validationException->getMessage(),
            strlen('OpenAPI document failed meta-schema validation: '),
        );
        expect(json_decode($jsonPart, true))->not->toBeNull();
    }
});

it('validation exception carries a structured errors array', function (): void {
    $openApi = OpenApi::create();   // missing info — fails validation

    try {
        $openApi->validate();
        expect(true)->toBeFalse('Expected ValidationException');
    } catch (ValidationException $validationException) {
        expect($validationException->errors())->toBeArray();
        expect($validationException->errors())->not->toBeEmpty();
    }
});

it('ValidationException constructed without errors returns empty array from errors()', function (): void {
    $e = new ValidationException('Something went wrong');
    expect($e->errors())->toBe([]);
});
