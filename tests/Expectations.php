<?php

declare(strict_types=1);

use Cortex\OpenApi\OpenApi;
use Cortex\OpenApi\Exceptions\ValidationException;

/**
 * Custom Pest expectations for OpenAPI meta-schema validation.
 * Method signatures for IDE/static analysis: tests/phpstan/pest-expectations.stub
 *
 * @param array<string, list<string>> $expectedErrors
 */
function assertOpenApiValidationErrors(OpenApi $openApi, array $expectedErrors): void
{
    try {
        $openApi->validate();
        test()->fail('Expected ValidationException');
    } catch (ValidationException $validationException) {
        foreach ($expectedErrors as $pointer => $messages) {
            expect($validationException->errors())->toHaveKey($pointer);
            foreach ($messages as $message) {
                expect($validationException->errors()[$pointer])->toContain($message);
            }
        }
    }
}

expect()->extend('toFailOpenApiValidation', function (array $expectedErrors): mixed {
    if (! $this->value instanceof OpenApi) {
        test()->fail('Expected an OpenApi instance.');
    }

    assertOpenApiValidationErrors($this->value, $expectedErrors);

    return $this;
});

expect()->extend('toFailOpenApiValidationAt', function (string $pointer, string $message): mixed {
    if (! $this->value instanceof OpenApi) {
        test()->fail('Expected an OpenApi instance.');
    }

    assertOpenApiValidationErrors($this->value, [
        $pointer => [$message],
    ]);

    return $this;
});

expect()->extend('toPassOpenApiValidation', function (): mixed {
    if (! $this->value instanceof OpenApi) {
        test()->fail('Expected an OpenApi instance.');
    }

    try {
        $this->value->validate();
    } catch (ValidationException $validationException) {
        test()->fail(
            'Expected OpenAPI document to pass validation: ' . $validationException->getMessage(),
        );
    }

    expect($this->value)->toBeInstanceOf(OpenApi::class);

    return $this;
});
