<?php

declare(strict_types=1);

namespace Cortex\OpenApi\Enums;

enum In: string
{
    case Query = 'query';
    case Header = 'header';
    case Path = 'path';
    case Cookie = 'cookie';
}
