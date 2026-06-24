<?php

declare(strict_types=1);

use Cortex\OpenApi\OpenApi;
use Cortex\OpenApi\Exceptions\ValidationException;

covers(OpenApi::class);

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
        $errors = $validationException->errors();
        expect($errors)->toBeArray();
        expect($errors)->not->toBeEmpty();
        expect($errors)->toHaveKey('/');
        expect($errors['/'])->toBeArray();
        expect($errors['/'][0])->toBe('The required properties (info) are missing');
    }
});

it('ValidationException constructed without errors returns empty array from errors()', function (): void {
    $e = new ValidationException('Something went wrong');
    expect($e->errors())->toBe([]);
});
