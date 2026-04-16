<?php

declare(strict_types=1);

use Cortex\OpenApi\Objects\Reference;
use Cortex\OpenApi\Concerns\HasExtensions;
use Cortex\OpenApi\Contracts\Serializable;
use Cortex\OpenApi\Objects\SecurityRequirement;

covers(Reference::class, SecurityRequirement::class, HasExtensions::class, Serializable::class);

arch('every class in Objects/ implements Serializable')
    ->expect('Cortex\OpenApi\Objects')
    ->toImplement(Serializable::class);

arch('every Objects/ class uses HasExtensions except Reference and SecurityRequirement')
    ->expect('Cortex\OpenApi\Objects')
    ->toUse(HasExtensions::class)
    ->ignoring([Reference::class, SecurityRequirement::class]);

arch('no Objects/ class is abstract')
    ->expect('Cortex\OpenApi\Objects')
    ->toBeFinal();

arch('Enums are backed enums')
    ->expect('Cortex\OpenApi\Enums')
    ->toBeEnum();
