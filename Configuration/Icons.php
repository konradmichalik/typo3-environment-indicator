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

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    'environment-indicator-widget' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:typo3_environment_indicator/Resources/Public/Icons/environment-indicator-widget.svg',
    ],
];
