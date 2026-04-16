<?php

declare(strict_types=1);

namespace Cortex\OpenApi\Concerns;

use Cortex\OpenApi\Contracts\Serializable;
use Cortex\JsonSchema\Contracts\JsonSchema;
use Cortex\OpenApi\Contracts\HasExtensionsInterface;

trait BuildsArray
{
    /**
     * Build an OpenAPI-shape array from the given fields, dropping any null values
     * and empty arrays, recursively unwrapping any Serializable children, and merging
     * any vendor extensions registered via HasExtensions.
     *
     * Keys listed in $alwaysInclude are emitted as-is, even when their value is
     * null or an empty array. This supports spec-allowed literal nulls (e.g.
     * Example::value, Parameter::example) and spec-allowed empty placeholders
     * (e.g. a PathItem operation key whose Operation has no fields yet).
     *
     * @param array<string, mixed> $fields
     * @param array<int, string>   $alwaysInclude
     *
     * @return array<string, mixed>
     */
    protected function buildArray(array $fields, array $alwaysInclude = []): array
    {
        $forced = array_flip($alwaysInclude);
        $output = [];

        foreach ($fields as $key => $value) {
            $force = isset($forced[$key]);

            if ($value === null && ! $force) {
                continue;
            }

            $unwrapped = $this->unwrapValue($value);

            if (is_array($unwrapped) && $unwrapped === [] && ! $force) {
                continue;
            }

            $output[$key] = $unwrapped;
        }

        if ($this instanceof HasExtensionsInterface) {
            foreach ($this->getExtensions() as $extensionKey => $extensionValue) {
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
