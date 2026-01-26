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

namespace KonradMichalik\Typo3EnvironmentIndicator\ViewHelpers;

use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Image\FaviconHandler;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Utility\{GeneralUtility, PathUtility};
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

use function array_key_exists;

/**
 * FaviconViewHelper.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class FaviconViewHelper extends AbstractViewHelper
{
    public function __construct(
        private readonly ExtensionConfiguration $extensionConfiguration,
    ) {}

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('favicon', 'string', 'Favicon path');
    }

    public function render(): string
    {
        $renderingContext = $this->renderingContext;
        if (null === $renderingContext) {
            return (string) $this->renderChildren();
        }
        $request = $renderingContext->getAttribute(ServerRequestInterface::class);
        $applicationType = ApplicationType::fromRequest($request);

        $favicon = $this->renderChildren();

        $extensionConfig = $this->extensionConfiguration->get(Configuration::EXT_KEY);
        if (($extensionConfig[$applicationType->value]['favicon'] ?? false) !== true
            || !array_key_exists(Environment::getContext()->__toString(), $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['context'])
            || !array_key_exists('favicon', $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['context'][Environment::getContext()->__toString()])
        ) {
            return $favicon;
        }

        if (!PathUtility::isExtensionPath($favicon)) {
            $favicon = Environment::getPublicPath().(str_contains((string) $favicon, '?') ? strtok($favicon, '?') : $favicon);
        }

        return GeneralUtility::makeInstance(FaviconHandler::class)->process($favicon);
    }
}
