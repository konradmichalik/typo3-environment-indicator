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

return [
    'frontend' => [
        'konradmichalik/typo3-environment-indicator/frontend-favicon' => [
            'target' => KonradMichalik\Typo3EnvironmentIndicator\Middleware\FrontendFaviconMiddleware::class,
            'after' => [
                'typo3/cms-frontend/prepare-tsfe-rendering',
            ],
        ],
        'konradmichalik/typo3-environment-indicator/frontend-page-title' => [
            'target' => KonradMichalik\Typo3EnvironmentIndicator\Middleware\FrontendPageTitleMiddleware::class,
            'after' => [
                'typo3/cms-frontend/prepare-tsfe-rendering',
            ],
        ],
    ],
    'backend' => [
        'konradmichalik/typo3-environment-indicator/backend-favicon' => [
            'target' => KonradMichalik\Typo3EnvironmentIndicator\Middleware\BackendFaviconMiddleware::class,
            'after' => [
                'typo3/cms-core/normalized-params-attribute',
            ],
        ],
        'konradmichalik/typo3-environment-indicator/backend-logo' => [
            'target' => KonradMichalik\Typo3EnvironmentIndicator\Middleware\BackendLogoMiddleware::class,
            'after' => [
                'typo3/cms-core/normalized-params-attribute',
            ],
        ],
    ],
];
