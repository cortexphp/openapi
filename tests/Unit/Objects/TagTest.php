<?php

declare(strict_types=1);

use Cortex\OpenApi\Objects\Tag;
use Cortex\OpenApi\Objects\ExternalDocs;

it('emits only name by default', function (): void {
    expect(Tag::create('Users')->toArray())->toBe(['name' => 'Users']);
});

it('emits description and external docs', function (): void {
    $tag = Tag::create('Users')
        ->description('User endpoints')
        ->externalDocs(ExternalDocs::create('https://example.com/docs/users'));

    expect($tag->toArray())->toBe([
        'name' => 'Users',
        'description' => 'User endpoints',
        'externalDocs' => ['url' => 'https://example.com/docs/users'],
    ]);
});
