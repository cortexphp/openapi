<?php

declare(strict_types=1);

namespace Cortex\OpenApi\Concerns;

use Cortex\OpenApi\Contracts\Serializable;
use Cortex\JsonSchema\Contracts\JsonSchema;

trait BuildsArray
{
    /**
     * Build an OpenAPI-shape array from the given fields, dropping any null values
     * and empty arrays, recursively unwrapping any Serializable children, and merging
     * any vendor extensions registered via HasExtensions.
     *
     * @param array<string, mixed> $fields
     *
     * @return array<string, mixed>
     */
    protected function buildArray(array $fields): array
    {
        $output = [];

        foreach ($fields as $key => $value) {
            if ($value === null) {
                continue;
            }

            $unwrapped = $this->unwrapValue($value);

            if (is_array($unwrapped) && $unwrapped === []) {
                continue;
            }

            $output[$key] = $unwrapped;
        }

        if (method_exists($this, 'getExtensions')) {
            /** @var array<string, mixed> $extensions */
            $extensions = $this->getExtensions();

            foreach ($extensions as $extensionKey => $extensionValue) {
                $output[$extensionKey] = $extensionValue;
            }
        }

        return $output;
    }

    private function unwrapValue(mixed $value): mixed
    {
        if ($value instanceof Serializable) {
            return $value->toArray();
        }

        if ($value instanceof JsonSchema) {
            // Inline schemas in OpenAPI must not carry the JSON Schema $schema URI or
            // a builder-assigned title, so strip both when embedding.
            return $value->toArray(includeSchemaRef: false, includeTitle: false);
        }

        if (is_array($value)) {
            $result = [];
            $isList = array_is_list($value);

            foreach ($value as $innerKey => $innerValue) {
                $unwrapped = $this->unwrapValue($innerValue);

                if ($isList) {
                    $result[] = $unwrapped;
                } else {
                    $result[$innerKey] = $unwrapped;
                }
            }

            return $result;
        }

        return $value;
    }
}
