<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\CodeQuality\Rector\Catch_\ThrowWithPreviousExceptionRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withParallel()
    ->withImportNames(
        importDocBlockNames: false,
        removeUnusedImports: true,
    )
    ->withPhpSets()
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        instanceOf: true,
        earlyReturn: true,
        naming: true,
    )
    ->withFluentCallNewLine()
    ->withSkip([
        // ThrowWithPreviousExceptionRector would add $throwable->getCode() which is int|string
        // but the Exception constructor only accepts int — forwarding the code is not possible without a cast.
        ThrowWithPreviousExceptionRector::class,
    ]);
