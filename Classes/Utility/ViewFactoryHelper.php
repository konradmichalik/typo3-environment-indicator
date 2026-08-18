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
        // render() treats its argument as a controller action name resolved against
        // templateRootPaths, never as a file path, so an EXT: path must instead be
        // handed to the view factory as templatePathAndFilename.
        $absoluteTemplatePath = PathUtility::isExtensionPath($template)
            ? GeneralUtility::getFileAbsFileName($template)
            : null;

        $viewFactoryData = new ViewFactoryData(
            templateRootPaths: ['EXT:'.Configuration::EXT_KEY.'/Resources/Private/Templates/'],
            partialRootPaths: ['EXT:'.Configuration::EXT_KEY.'/Resources/Private/Partials/'],
            layoutRootPaths: ['EXT:'.Configuration::EXT_KEY.'/Resources/Private/Layouts/'],
            templatePathAndFilename: $absoluteTemplatePath,
            request: $request,
        );

        $viewFactory = GeneralUtility::makeInstance(ViewFactoryInterface::class);
        $view = $viewFactory->create($viewFactoryData);
        $view->assignMultiple($values);

        return $view->render(null === $absoluteTemplatePath ? $template : '');
    }
}
