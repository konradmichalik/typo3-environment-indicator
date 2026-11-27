<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_environment_indicator" TYPO3 CMS extension.
 *
 * (c) 2025-2026 Konrad Michalik <hej@konradmichalik.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Composer\Autoload;
use ShipMonk\ComposerDependencyAnalyser;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

$rootPath = dirname(__DIR__, 2);

/** @var Autoload\ClassLoader $loader */
$loader = require $rootPath.'/vendor/autoload.php';
$loader->register();

$configuration = new ComposerDependencyAnalyser\Config\Configuration();
$configuration
    ->addPathToScan($rootPath.'/Configuration', false)
    ->addPathsToExclude([
        $rootPath.'/Tests/CGL',
    ])
    // Optional image drivers, see "suggest" in composer.json. Depending on the PHP
    // installation they show up as unknown classes or as the extension itself, so
    // unmatched ignores must not fail (this applies to all ignores).
    ->disableReportingUnmatchedIgnores()
    ->ignoreUnknownClasses([
        'Imagick',
        'ImagickPixel',
        'Intervention\\Image\\Drivers\\Vips\\Driver',
    ])
    ->ignoreErrorsOnExtension('ext-gd', [ErrorType::SHADOW_DEPENDENCY])
    ->ignoreErrorsOnExtension('ext-imagick', [ErrorType::SHADOW_DEPENDENCY])
    // Only the Fluid templates use it (f:asset.css, f:asset.script, f:translate), which the analyser cannot see
    ->ignoreErrorsOnPackage('typo3/cms-fluid', [ErrorType::PROD_DEPENDENCY_ONLY_IN_DEV])
;

return $configuration;
