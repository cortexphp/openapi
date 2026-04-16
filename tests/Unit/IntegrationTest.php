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
            Info::create()
                ->title('Swagger Petstore')
                ->version('1.0.0')
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
                                ->content(MediaType::json(Reference::to('#/components/schemas/Pet'))),
                        ),
                    Operation::post()
                        ->operationId('createPet')
                        ->tags('pets')
                        ->requestBody(
                            RequestBody::create()
                                ->required(true)
                                ->content(MediaType::json(Reference::to('#/components/schemas/Pet'))),
                        )
                        ->responses(
                            Response::created()
                                ->content(MediaType::json(Reference::to('#/components/schemas/Pet'))),
                            Response::default()
                                ->description('Unexpected error')
                                ->content(MediaType::json(Reference::to('#/components/schemas/Error'))),
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
                ->parameters(Reference::to('#/components/parameters/PetId'))
                ->operations(
                    Operation::get()
                        ->operationId('showPetById')
                        ->tags('pets')
                        ->responses(
                            Response::ok()->content(MediaType::json(Reference::to('#/components/schemas/Pet'))),
                            Response::notFound()->content(MediaType::json(Reference::to('#/components/schemas/Error'))),
                        ),
                ),
        )
        ->webhooks([
            'pet.deleted' => PathItem::create('/pet.deleted')->operations(
                Operation::post()
                    ->operationId('petDeletedWebhook')
                    ->responses(Response::ok()),
            ),
        ])
        ->x('x-api-id', 'petstore-1');
}

it('builds a petstore-style document and validates against meta-schema', function (): void {
    $openApi = buildPetstore();
    $openApi->validate();

    $arr = $openApi->toArray();

    expect($arr['openapi'])->toBe('3.1.0');
    expect($arr['info']['title'])->toBe('Swagger Petstore');
    expect($arr['x-api-id'])->toBe('petstore-1');
    expect($arr['paths']['/pets']['get']['operationId'])->toBe('listPets');
    expect($arr['paths']['/pets']['post']['callbacks']['onPetCreate'])->toHaveKey('{$request.body#/webhookUrl}');
    expect($arr['webhooks']['pet.deleted']['post']['operationId'])->toBe('petDeletedWebhook');
    expect($arr['components']['securitySchemes']['OAuth2']['type'])->toBe('oauth2');
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
        ->info(Info::create()->title('x')->version('1'))
        ->paths(
            PathItem::create('/foo')->operations(
                Operation::get()->responses(
                    Response::ok()->content(MediaType::json(Schema::object('Foo')->properties(Schema::string('bar')))),
                ),
            ),
        );

    $inline = $openApi->toArray()['paths']['/foo']['get']['responses']['200']['content']['application/json']['schema'];

    expect($inline)->not->toHaveKey('$schema');
    expect($inline)->not->toHaveKey('title');
    expect($inline)->toBe([
        'type' => 'object',
        'properties' => [
            'bar' => [
                'type' => 'string',
            ],
        ],
    ]);
});
