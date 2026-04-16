<?php

declare(strict_types=1);

namespace Cortex\OpenApi\Enums;

enum OpenApiVersion: string
{
    case V3_1_0 = '3.1.0';
    case V3_1_1 = '3.1.1';

    public static function default(): self
    {
        return self::V3_1_0;
    }
}
