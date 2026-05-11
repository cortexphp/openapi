<?php

declare(strict_types=1);

use Cortex\JsonSchema\Schema;
use Cortex\OpenApi\Objects\Example;
use Cortex\OpenApi\Objects\Encoding;
use Cortex\OpenApi\Objects\MediaType;
use Cortex\OpenApi\Objects\Reference;

covers(MediaType::class);

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
    $mediaType = MediaType::json(Schema::object('Ignored')->properties(
        Schema::string('id'),
    ));

    expect($mediaType->toArray())->toBe([
        'schema' => [
            'type' => 'object',
            'properties' => [
                'id' => [
                    'type' => 'string',
                ],
            ],
        ],
    ]);
});

it('accepts a Reference', function (): void {
    $mediaType = MediaType::json(Reference::schema('User'));

    expect($mediaType->toArray())->toBe([
        'schema' => [
            '$ref' => '#/components/schemas/User',
        ],
    ]);
});

it('supports example and examples', function (): void {
    $mediaType = MediaType::json(Schema::string())
        ->example('foo')
        ->examples([
            'first' => Example::create()->value('a'),
            'second' => Reference::example('Second'),
        ]);

    expect($mediaType->toArray())->toBe([
        'schema' => [
            'type' => 'string',
        ],
        'example' => 'foo',
        'examples' => [
            'first' => [
                'value' => 'a',
            ],
            'second' => [
                '$ref' => '#/components/examples/Second',
            ],
        ],
    ]);
});

it('example appears before encoding in output', function (): void {
    $mediaType = MediaType::json(Schema::string())
        ->example('foo')
        ->encoding([
            'photo' => Encoding::create()->contentType('image/png'),
        ]);

    $keys = array_keys($mediaType->toArray());
    expect(array_search('example', $keys, true))->toBeLessThan(array_search('encoding', $keys, true));
});

it('example appears at end when only schema present', function (): void {
    $mediaType = MediaType::json(Schema::string())->example('foo');

    expect($mediaType->toArray())->toBe([
        'schema' => [
            'type' => 'string',
        ],
        'example' => 'foo',
    ]);
});

it('emits encoding', function (): void {
    $mediaType = MediaType::json(Schema::object())
        ->encoding([
            'photo' => Encoding::create()->contentType('image/png'),
        ]);

    expect($mediaType->toArray())->toHaveKey('encoding');
});
