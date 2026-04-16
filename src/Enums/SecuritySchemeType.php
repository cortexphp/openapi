<?php

declare(strict_types=1);

namespace Cortex\OpenApi\Enums;

enum SecuritySchemeType: string
{
    case ApiKey = 'apiKey';
    case Http = 'http';
    case MutualTls = 'mutualTLS';
    case OAuth2 = 'oauth2';
    case OpenIdConnect = 'openIdConnect';
}
