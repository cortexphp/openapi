# Fluently build OpenAPI 3.1 specs

[![Latest Version](https://img.shields.io/packagist/v/cortexphp/openapi.svg?style=flat-square&logo=composer)](https://packagist.org/packages/cortexphp/openapi)
![GitHub License](https://img.shields.io/github/license/cortexphp/openapi?style=flat-square&logo=github)

A modern, fluent builder for [OpenAPI 3.1](https://spec.openapis.org/oas/v3.1.0) specs, built on top of [`cortexphp/json-schema`](https://github.com/cortexphp/json-schema) so that schemas are first-class JSON Schema 2020-12 and everything plays nicely with the rest of the Cortex ecosystem.

## Features

- Full OpenAPI 3.1 object graph (paths, webhooks, callbacks, components, security, tags)
- Schemas via `cortexphp/json-schema` — no parallel schema DSL to learn
- Vendor extensions (`x-*`) and `$ref` on every object
- JSON and YAML output (YAML via optional `symfony/yaml`)
- Meta-schema validation proxied through the existing `cortex/json-schema` pipeline

## Requirements

- PHP 8.3+

## Installation

```bash
composer require cortexphp/openapi
```

For YAML output:

```bash
composer require symfony/yaml
```

## Quick Start

```php
use Cortex\JsonSchema\Schema;
use Cortex\JsonSchema\Enums\SchemaFormat;
use Cortex\OpenApi\OpenApi;
use Cortex\OpenApi\Objects\{Info, Tag, PathItem, Operation, Response, MediaType, Parameter, Components, Reference};

$userSchema = Schema::object('User')->properties(
    Schema::string('id')->format(SchemaFormat::Uuid)->required(),
    Schema::string('name')->required(),
    Schema::integer('age')->minimum(0),
);

$openApi = OpenApi::create()
    ->info(Info::create()->title('Example API')->version('1.0.0'))
    ->tags(Tag::create('Users')->description('User endpoints'))
    ->components(Components::create()->schema('User', $userSchema))
    ->paths(
        PathItem::create('/users/{id}')
            ->parameters(Parameter::path('id', Schema::string()->format(SchemaFormat::Uuid)))
            ->operations(
                Operation::get()
                    ->operationId('users.show')
                    ->tags('Users')
                    ->responses(
                        Response::ok()->content(MediaType::json(Reference::to('#/components/schemas/User'))),
                        Response::notFound(),
                    ),
            ),
    );

echo $openApi->toJson(JSON_PRETTY_PRINT);
```
## Documentation

📚 **[View Full Documentation →](https://docs.cortexphp.com/openapi)**

## Credits

- [Sean Tymon](https://github.com/tymondesigns)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
