<?php

declare(strict_types=1);

use Cortex\JsonSchema\Schema;
use Cortex\OpenApi\Objects\Example;
use Cortex\OpenApi\Objects\Reference;
use Cortex\OpenApi\Objects\MediaType;

it('knows its own content type', function (): void {
    expect(MediaType::json()->getContentType())->toBe('application/json');
    expect(MediaType::xml()->getContentType())->toBe('application/xml');
    expect(MediaType::form()->getContentType())->toBe('application/x-www-form-urlencoded');
    expect(MediaType::multipart()->getContentType())->toBe('multipart/form-data');
    expect(MediaType::text()->getContentType())->toBe('text/plain');
    expect(MediaType::html()->getContentType())->toBe('text/html');
    expect(MediaType::octetStream()->getContentType())->toBe('application/octet-stream');
    expect(MediaType::of('application/pdf')->getContentType())->toBe('application/pdf');
});

it('accepts a JsonSchema and strips $schema/title when serialized', function (): void {
    $media = MediaType::json(Schema::object('Ignored')->properties(
        Schema::string('id'),
    ));

    expect($media->toArray())->toBe([
        'schema' => [
            'type' => 'object',
            'properties' => ['id' => ['type' => 'string']],
        ],
    ]);
});

it('accepts a Reference', function (): void {
    $media = MediaType::json(Reference::to('#/components/schemas/User'));

    expect($media->toArray())->toBe([
        'schema' => ['$ref' => '#/components/schemas/User'],
    ]);
});

it('supports example and examples', function (): void {
    $media = MediaType::json(Schema::string())
        ->example('foo')
        ->examples([
            'first' => Example::create()->value('a'),
            'second' => Reference::to('#/components/examples/Second'),
        ]);

    expect($media->toArray())->toBe([
        'schema' => ['type' => 'string'],
        'example' => 'foo',
        'examples' => [
            'first' => ['value' => 'a'],
            'second' => ['$ref' => '#/components/examples/Second'],
        ],
    ]);
});
