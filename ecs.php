<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withPreparedSets(
        psr12: true,
        common: true,
        symplify: true,
    )
    ->withSkip([
        NotOperatorWithSuccessorSpaceFixer::class,
    ])
    ->withRules([
        NoUnusedImportsFixer::class,
    ]);
