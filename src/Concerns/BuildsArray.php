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
            foreach ($this->getExtensions() as $extensionKey => $extensionValue) { // @phpstan-ignore foreach.nonIterable
                $output[$extensionKey] = $extensionValue; // @phpstan-ignore offsetAccess.invalidOffset
            }
        }

        return $output; // @phpstan-ignore return.type
    }

    private function unwrapValue(mixed $value): mixed
    {
        if ($value instanceof Serializable) {
            return $value->toArray();
        }

        if ($value instanceof JsonSchema) {
            // Inline schemas must not carry the JSON Schema $schema URI. Constructor
            // titles are builder-assigned names and are stripped; a title() value that
            // differs from getInitialTitle() was set deliberately and is kept.
            $title = $value->getTitle();
            $includeTitle = $title !== null && $title !== $value->getInitialTitle();

            return $this->stripSchemaRef(
                $value->toArray(includeSchemaRef: false, includeTitle: $includeTitle),
            );
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

    /**
     * $schema must not appear outside the root of a schema resource (JSON Schema
     * 2020-12 core, 8.1.1), and the builder cannot produce a nested resource root, so
     * every nested occurrence is invalid. Some cortexphp/json-schema versions emit it
     * anyway for items and contains. A raw array schema is left alone, which is the
     * way to declare a dialect deliberately.
     *
     * @param array<array-key, mixed> $schema
     *
     * @return array<array-key, mixed>
     */
    private function stripSchemaRef(array $schema): array
    {
        // Keys holding a map of subschemas, where the map keys are user-chosen names
        // (a property may legitimately be named "$schema") rather than keywords.
        $namedSubschemaKeys = ['properties', 'patternProperties', 'dependentSchemas', '$defs', 'definitions'];

        unset($schema['$schema']);

        foreach ($schema as $key => $value) {
            if (! is_array($value)) {
                continue;
            }

            if (in_array($key, $namedSubschemaKeys, true)) {
                foreach ($value as $name => $subschema) {
                    if (is_array($subschema)) {
                        $value[$name] = $this->stripSchemaRef($subschema);
                    }
                }

                $schema[$key] = $value;

                continue;
            }

            $schema[$key] = $this->stripSchemaRef($value);
        }

        return $schema;
    }
}
