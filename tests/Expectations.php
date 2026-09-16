<?php

declare(strict_types=1);

use Cortex\OpenApi\OpenApi;
use PHPUnit\Framework\Assert;
use Cortex\OpenApi\Exceptions\ValidationException;

/**
 * @return array<mixed>
 */
function expectArray(mixed $value): array
{
    expect($value)->toBeArray();

    if (! is_array($value)) {
        Assert::fail('Expected array.');
    }

    return $value;
}

/**
 * @param array<int, int|string> $keys
 */
function keyIndex(array $keys, string $key): int
{
    $index = array_search($key, $keys, true);
    expect($index)->toBeInt();

    if (! is_int($index)) {
        Assert::fail("Expected key [{$key}] to exist.");
    }

    return $index;
}

function assertOpenApiValidationPasses(OpenApi $openApi): void
{
    try {
        $openApi->validate();
    } catch (ValidationException $validationException) {
        Assert::fail(
            'Expected OpenAPI document to pass validation: ' . $validationException->getMessage(),
        );
    }

    Assert::assertInstanceOf(OpenApi::class, $openApi);
}

/**
 * @param array<string, list<string>> $expectedErrors
 */
function assertOpenApiValidationErrors(OpenApi $openApi, array $expectedErrors): void
{
    try {
        $openApi->validate();
        Assert::fail('Expected ValidationException');
    } catch (ValidationException $validationException) {
        foreach ($expectedErrors as $pointer => $messages) {
            expect($validationException->errors())->toHaveKey($pointer);
            foreach ($messages as $message) {
                expect($validationException->errors()[$pointer])->toContain($message);
            }
        }
    }
}

function assertOpenApiValidationFailsAt(OpenApi $openApi, string $pointer, string $message): void
{
    assertOpenApiValidationErrors($openApi, [
        $pointer => [$message],
    ]);
}
