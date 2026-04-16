<?php

declare(strict_types=1);

use Cortex\OpenApi\Objects\Info;
use Cortex\OpenApi\Objects\Contact;
use Cortex\OpenApi\Objects\License;

covers(Info::class, Contact::class, License::class);

it('emits title and version', function (): void {
    $info = Info::create('API', '1.0.0');

    expect($info->toArray())->toBe([
        'title' => 'API',
        'version' => '1.0.0',
    ]);
});

it('emits every optional field when set', function (): void {
    $info = Info::create('Example API', '1.2.3')
        ->summary('Short summary')
        ->description('Long description')
        ->termsOfService('https://example.com/terms')
        ->contact(Contact::create()->name('API Team'))
        ->license(License::create('MIT'))
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
    $info = Info::create('API', '1.0')
        ->description('x')
        ->description(null);

    expect($info->toArray())->toBe([
        'title' => 'API',
        'version' => '1.0',
    ]);
});

it('contact emits all fields', function (): void {
    $contact = Contact::create()
        ->name('API Team')
        ->url('https://example.com/support')
        ->email('support@example.com');

    expect($contact->toArray())->toBe([
        'name' => 'API Team',
        'url' => 'https://example.com/support',
        'email' => 'support@example.com',
    ]);
});

it('license emits identifier and url', function (): void {
    $license = License::create('Apache 2.0')
        ->identifier('Apache-2.0')
        ->url('https://www.apache.org/licenses/LICENSE-2.0');

    expect($license->toArray())->toBe([
        'name' => 'Apache 2.0',
        'identifier' => 'Apache-2.0',
        'url' => 'https://www.apache.org/licenses/LICENSE-2.0',
    ]);
});
