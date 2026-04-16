<?php

declare(strict_types=1);

use Cortex\OpenApi\Objects\Server;
use Cortex\OpenApi\Objects\ServerVariable;

it('emits only url by default', function (): void {
    expect(Server::create('https://api.example.com')->toArray())->toBe([
        'url' => 'https://api.example.com',
    ]);
});

it('emits description and variables', function (): void {
    $server = Server::create('https://{env}.example.com/{version}')
        ->description('Tenant server')
        ->variables(
            ServerVariable::create('env', 'prod')->enum(['prod', 'staging'])->description('Env tier'),
            ServerVariable::create('version', 'v1'),
        );

    expect($server->toArray())->toBe([
        'url' => 'https://{env}.example.com/{version}',
        'description' => 'Tenant server',
        'variables' => [
            'env' => [
                'default' => 'prod',
                'enum' => ['prod', 'staging'],
                'description' => 'Env tier',
            ],
            'version' => [
                'default' => 'v1',
            ],
        ],
    ]);
});
