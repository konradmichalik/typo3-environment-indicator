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

namespace KonradMichalik\Typo3EnvironmentIndicator\Utility;

use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\{GeneralUtility, PathUtility};
use TYPO3\CMS\Core\View\{ViewFactoryData, ViewFactoryInterface};

/**
 * ViewFactoryHelper.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ViewFactoryHelper
{
    /**
     * @param array<string, mixed> $values
     */
    public static function renderView(string $template, array $values, ?ServerRequestInterface $request = null): string
    {
        $viewFactoryData = new ViewFactoryData(
            templateRootPaths: ['EXT:'.Configuration::EXT_KEY.'/Resources/Private/Templates/'],
            partialRootPaths: ['EXT:'.Configuration::EXT_KEY.'/Resources/Private/Partials/'],
            layoutRootPaths: ['EXT:'.Configuration::EXT_KEY.'/Resources/Private/Layouts/'],
            request: $request,
        );

        $viewFactory = GeneralUtility::makeInstance(ViewFactoryInterface::class);
        $view = $viewFactory->create($viewFactoryData);
        $view->assignMultiple($values);

        if (PathUtility::isExtensionPath($template)) {
            $template = GeneralUtility::getFileAbsFileName($template);
        }

        return $view->render($template);
    }
}
