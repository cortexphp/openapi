<?php

declare(strict_types=1);

use Cortex\OpenApi\Objects\Info;
use Cortex\OpenApi\Objects\Contact;
use Cortex\OpenApi\Objects\License;

covers(Info::class, Contact::class, License::class);

it('emits only title and version by default', function (): void {
    $info = Info::create()->title('API')->version('1.0.0');

    expect($info->toArray())->toBe([
        'title' => 'API',
        'version' => '1.0.0',
    ]);
});

it('emits every optional field when set', function (): void {
    $info = Info::create()
        ->title('Example API')
        ->summary('Short summary')
        ->description('Long description')
        ->termsOfService('https://example.com/terms')
        ->contact(Contact::create()->name('API Team'))
        ->license(License::create('MIT'))
        ->version('1.2.3')
        ->x('audience', 'public');

    expect($info->toArray())->toBe([
        'title' => 'Example API',
        'summary' => 'Short summary',
        'description' => 'Long description',
        'termsOfService' => 'https://example.com/terms',
        'contact' => [
            'name' => 'API Team',
        ],
        'license' => [
            'name' => 'MIT',
        ],
        'version' => '1.2.3',
        'x-audience' => 'public',
    ]);
});

it('drops unset optional fields', function (): void {
    $info = Info::create()
        ->title('API')
        ->version('1.0')
        ->description('x')
        ->description(null);

    expect($info->toArray())->toBe([
        'title' => 'API',
        'version' => '1.0',
    ]);
});
