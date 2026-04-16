<?php

declare(strict_types=1);

namespace Cortex\OpenApi\Exceptions;

use Throwable;

final class ValidationException extends OpenApiException
{
    /**
     * @param array<mixed> $errors
     */
    public function __construct(
        string $message,
        private readonly array $errors = [],
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    /**
     * The structured validation errors as returned by the OpenAPI meta-schema validator.
     * Keys are JSON pointers; values are lists of error messages or nested error arrays.
     *
     * @return array<mixed>
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
