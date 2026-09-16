<?php

declare(strict_types=1);

use Cortex\OpenApi\OpenApi;
use Cortex\JsonSchema\Schema;
use Cortex\OpenApi\Objects\Tag;
use Cortex\OpenApi\Objects\Info;
use Symfony\Component\Yaml\Yaml;
use Cortex\OpenApi\Objects\Server;
use Cortex\OpenApi\Objects\Callback;
use Cortex\OpenApi\Objects\PathItem;
use Cortex\OpenApi\Objects\Response;
use Cortex\OpenApi\Objects\MediaType;
use Cortex\OpenApi\Objects\OAuthFlow;
use Cortex\OpenApi\Objects\Operation;
use Cortex\OpenApi\Objects\Parameter;
use Cortex\OpenApi\Objects\Reference;
use Cortex\OpenApi\Objects\Components;
use Cortex\OpenApi\Objects\OAuthFlows;
use Cortex\OpenApi\Objects\RequestBody;
use Cortex\OpenApi\Objects\SecurityScheme;
use Cortex\OpenApi\Objects\SecurityRequirement;

covers(OpenApi::class);

function buildPetstore(): OpenApi
{
    $objectSchema = Schema::object('Pet')->properties(
        Schema::integer('id')->format('int64')->required(),
        Schema::string('name')->required(),
        Schema::string('tag'),
    );

    $errorSchema = Schema::object('Error')->properties(
        Schema::integer('code')->format('int32')->required(),
        Schema::string('message')->required(),
    );

    return OpenApi::create()
        ->info(
            Info::create('Swagger Petstore', '1.0.0')
                ->description('A minimal example of the Petstore API.'),
        )
        ->servers(Server::create('https://petstore.example.com/v1'))
        ->tags(Tag::create('pets')->description('Everything about pets'))
        ->components(
            Components::create()
                ->schema('Pet', $objectSchema)
                ->schema('Error', $errorSchema)
                ->securityScheme('OAuth2', SecurityScheme::oauth2(
                    OAuthFlows::create()->authorizationCode(
                        OAuthFlow::create()
                            ->authorizationUrl('https://petstore.example.com/oauth/authorize')
                            ->tokenUrl('https://petstore.example.com/oauth/token')
                            ->scopes([
                                'read:pets' => 'Read pets',
                                'write:pets' => 'Modify pets',
                            ]),
                    ),
                ))
                ->parameter('PetId', Parameter::path('petId', Schema::integer()->format('int64'))),
        )
        ->security(SecurityRequirement::create('OAuth2', ['read:pets']))
        ->paths(
            PathItem::create('/pets')
                ->operations(
                    Operation::get()
                        ->operationId('listPets')
                        ->tags('pets')
                        ->parameters(
                            Parameter::query('limit', Schema::integer()->minimum(1)->maximum(100))
                                ->description('How many items to return at one time (max 100)'),
                        )
                        ->responses(
                            Response::ok()
                                ->description('A paged array of pets')
                                ->json(Reference::schema('Pet')),
                        ),
                    Operation::post()
                        ->operationId('createPet')
                        ->tags('pets')
                        ->requestBody(
                            RequestBody::create()
                                ->required(true)
                                ->json(Reference::schema('Pet')),
                        )
                        ->responses(
                            Response::created()
                                ->json(Reference::schema('Pet')),
                            Response::default()
                                ->description('Unexpected error')
                                ->json(Reference::schema('Error')),
                        )
                        ->callbacks([
                            'onPetCreate' => Callback::create()->expression(
                                '{$request.body#/webhookUrl}',
                                PathItem::create('/webhook')->operations(
                                    Operation::post()
                                        ->operationId('petCreatedCallback')
                                        ->responses(Response::ok()),
                                ),
                            ),
                        ]),
                ),
            PathItem::create('/pets/{petId}')
                ->parameters(Reference::parameter('PetId'))
                ->operations(
                    Operation::get()
                        ->operationId('showPetById')
                        ->tags('pets')
                        ->responses(
                            Response::ok()->json(Reference::schema('Pet')),
                            Response::notFound()->json(Reference::schema('Error')),
                        ),
                ),
        )
        ->webhooks([
            'pet.deleted' => PathItem::create('/pet.deleted')->operations(
                Operation::post()
                    ->operationId('petDeletedWebhook')
                    ->requestBody(RequestBody::create()->json(Reference::schema('Pet')))
                    ->responses(Response::ok()),
            ),
        ])
        ->x('x-api-id', 'petstore-1');
}

it('builds a petstore-style document and validates against meta-schema', function (): void {
    $openApi = buildPetstore();
    $openApi->validate();

    $arr = $openApi->toArray();
    $info = expectArray($arr['info']);
    $paths = expectArray($arr['paths']);
    $pets = expectArray($paths['/pets']);
    $get = expectArray($pets['get']);
    $post = expectArray($pets['post']);
    $callbacks = expectArray($post['callbacks']);
    $webhooks = expectArray($arr['webhooks']);
    $petDeleted = expectArray($webhooks['pet.deleted']);
    $petDeletedPost = expectArray($petDeleted['post']);
    $components = expectArray($arr['components']);
    $securitySchemes = expectArray($components['securitySchemes']);
    $oauth2 = expectArray($securitySchemes['OAuth2']);

    expect($arr['openapi'])->toBe('3.1.0')
        ->and($info['title'])
        ->toBe('Swagger Petstore')
        ->and($arr['x-api-id'])
        ->toBe('petstore-1')
        ->and($get['operationId'])
        ->toBe('listPets')
        ->and($callbacks['onPetCreate'])
        ->toHaveKey('{$request.body#/webhookUrl}')
        ->and($petDeletedPost['operationId'])
        ->toBe('petDeletedWebhook')
        ->and($oauth2['type'])
        ->toBe('oauth2');
});

it('round-trips through JSON encoding', function (): void {
    $openApi = buildPetstore();
    $json = $openApi->toJson();

    expect(json_decode($json, true))->toBe($openApi->toArray());
});

it('round-trips through YAML encoding', function (): void {
    $openApi = buildPetstore();

    expect(class_exists(Yaml::class))
        ->toBeTrue('symfony/yaml must be installed in dev');

    $yaml = $openApi->toYaml();

    expect(Yaml::parse($yaml))->toBe($openApi->toArray());
});

it('embeds schemas without $schema or title', function (): void {
    // Inline schemas must not carry the JSON Schema $schema URI or a builder-assigned title.
    $openApi = OpenApi::create()
        ->info(Info::create('x', '1'))
        ->paths(
            PathItem::create('/foo')->operations(
                Operation::get()->responses(
                    Response::ok()->content(MediaType::json(Schema::object('Foo')->properties(Schema::string('bar')))),
                ),
            ),
        );

    $paths = expectArray($openApi->toArray()['paths']);
    $foo = expectArray($paths['/foo']);
    $get = expectArray($foo['get']);
    $responses = expectArray($get['responses']);
    $ok = expectArray($responses['200']);
    $content = expectArray($ok['content']);
    $json = expectArray($content['application/json']);
    $inline = $json['schema'];

    expect($inline)->not->toHaveKey('$schema')->not->toHaveKey('title')
        ->toBe([
            'type' => 'object',
            'properties' => [
                'bar' => [
                    'type' => 'string',
                ],
            ],
        ]);
});
