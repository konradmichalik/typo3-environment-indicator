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

use KonradMichalik\Typo3EnvironmentIndicator\Configuration\{Indicator, Trigger};
use KonradMichalik\Typo3EnvironmentIndicator\{Configuration, Image};

defined('TYPO3') || exit;

Configuration::addToolbarItems();

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1744624800] = [
    'nodeName' => 'environmentIndicatorThemeInfo',
    'priority' => 40,
    'class' => KonradMichalik\Typo3EnvironmentIndicator\Backend\Form\Element\ThemeInfoElement::class,
];

// Default configuration
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] = [];
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['defaults'] = [
    Image\Modifier\TextModifier::class => [
        'font' => 'EXT:typo3_environment_indicator/Resources/Public/Fonts/OpenSans-Bold.ttf',
        'position' => 'top',
    ],
    Image\Modifier\TriangleModifier::class => [
        'size' => 0.7,
        'position' => 'bottom right',
    ],
    Image\Modifier\CircleModifier::class => [
        'size' => 0.4,
        'padding' => 0.1,
        'position' => 'bottom right',
    ],
    Image\Modifier\FrameModifier::class => [
        'borderSize' => 5,
    ],
    Image\Modifier\ColorizeModifier::class => [
        'opacity' => 1,
    ],
    Image\Modifier\OverlayModifier::class => [
        'size' => 0.5,
        'padding' => 0.1,
        'position' => 'bottom right',
    ],
    Indicator\Favicon::class => [
        '_path' => 'typo3temp/assets/favicons/',
    ],
    Indicator\Backend\Logo::class => [
        '_path' => 'typo3temp/assets/images/',
    ],
    Indicator\Frontend\Image::class => [
        '_path' => 'typo3temp/assets/images/',
    ],
    Indicator\Backend\Toolbar::class => [
        'icon' => [
            'context' => 'information-application-context',
        ],
        'index' => 0,
    ],
    Indicator\Frontend\Hint::class => [
        'position' => 'top left',
    ],
    Indicator\Backend\Theme::class => [
        'scaffoldHeader' => true,
        'scaffoldSidebar' => true,
        'neutralMix' => '15%',
    ],
    Indicator\Cli\Banner::class => [
        'icon' => '🚦',
    ],
    Indicator\Mail\SubjectPrefix::class => [
        'prefix' => '[%context%] ',
    ],
    Indicator\General\PageTitle::class => [
        'prefix' => '[%context%] ',
    ],
];

// Presets
if ($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Configuration::EXT_KEY]['general']['defaultConfiguration'] ?? false) {
    Configuration\Handler::addIndicator(
        triggers: [
            new Trigger\ApplicationContext('Development*'),
        ],
        indicators: [
            new Indicator\Favicon([
                new Image\Modifier\TextModifier([
                    'text' => 'DEV',
                    'color' => '#bd593a',
                    'stroke' => [
                        'color' => '#ffffff',
                        'width' => 3,
                    ],
                ]),
            ]),
            new Indicator\Backend\Logo([
                new Image\Modifier\TextModifier([
                    'text' => 'DEV',
                    'color' => '#bd593a',
                    'stroke' => [
                        'color' => '#ffffff',
                        'width' => 3,
                    ],
                ]),
            ]),
            new Indicator\Frontend\Hint([
                'color' => '#bd593a',
            ]),
            new Indicator\Backend\Toolbar([
                'color' => '#bd593a',
            ]),
            new Indicator\Backend\Widget([
                'color' => '#bd593a',
            ]),
            new Indicator\Backend\Theme([
                'color' => '#bd593a',
            ]),
            new Indicator\Cli\Banner([
                'color' => '#bd593a',
            ]),
            new Indicator\Mail\SubjectPrefix(),
            new Indicator\General\PageTitle(),
        ],
    );

    Configuration\Handler::addIndicator(
        triggers: [
            new Trigger\ApplicationContext('Testing*'),
        ],
        indicators: [
            new Indicator\Favicon([
                new Image\Modifier\TextModifier([
                    'text' => 'TEST',
                    'color' => '#f39c12',
                    'stroke' => [
                        'color' => '#ffffff',
                        'width' => 3,
                    ],
                ]),
            ]),
            new Indicator\Backend\Logo([
                new Image\Modifier\TextModifier([
                    'text' => 'TEST',
                    'color' => '#f39c12',
                    'stroke' => [
                        'color' => '#ffffff',
                        'width' => 3,
                    ],
                ]),
            ]),
            new Indicator\Frontend\Hint([
                'color' => '#f39c12',
            ]),
            new Indicator\Backend\Toolbar([
                'color' => '#f39c12',
            ]),
            new Indicator\Backend\Theme([
                'color' => '#f39c12',
            ]),
            new Indicator\Cli\Banner([
                'color' => '#f39c12',
            ]),
            new Indicator\Mail\SubjectPrefix(),
            new Indicator\General\PageTitle(),
        ],
    );

    Configuration\Handler::addIndicator(
        triggers: [
            new Trigger\ApplicationContext('Production/Staging', 'Production/Stage'),
        ],
        indicators: [
            new Indicator\Favicon([
                new Image\Modifier\TextModifier([
                    'text' => 'STG',
                    'color' => '#2f9c91',
                    'stroke' => [
                        'color' => '#ffffff',
                        'width' => 3,
                    ],
                ]),
            ]),
            new Indicator\Backend\Logo([
                new Image\Modifier\TextModifier([
                    'text' => 'STG',
                    'color' => '#2f9c91',
                    'stroke' => [
                        'color' => '#ffffff',
                        'width' => 3,
                    ],
                ]),
            ]),
            new Indicator\Frontend\Hint([
                'color' => '#2f9c91',
            ]),
            new Indicator\Backend\Toolbar([
                'color' => '#2f9c91',
            ]),
            new Indicator\Backend\Theme([
                'color' => '#2f9c91',
            ]),
            new Indicator\Cli\Banner([
                'color' => '#2f9c91',
            ]),
            new Indicator\Mail\SubjectPrefix(),
            new Indicator\General\PageTitle(),
        ],
    );

    Configuration\Handler::addIndicator(
        triggers: [
            new Trigger\ApplicationContext('Production/Standby'),
        ],
        indicators: [
            new Indicator\Backend\Toolbar([
                'color' => '#2f9c91',
            ]),
            new Indicator\Cli\Banner([
                'color' => '#2f9c91',
            ]),
            new Indicator\General\PageTitle(),
        ],
    );
}
