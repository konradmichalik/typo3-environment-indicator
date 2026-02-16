..  include:: /Includes.rst.txt

..  _example:

=======================
Example
=======================

The following configuration is also the default configuration. This shall show the usage of the extension.

..  code-block:: php
    :caption: ext_localconf.php

    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Handler;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger;
    use KonradMichalik\Typo3EnvironmentIndicator\Image;

    Handler::addIndicator(
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
        ],
    );

    Handler::addIndicator(
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
        ],
    );

    Handler::addIndicator(
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
        ],
    );

    Handler::addIndicator(
        triggers: [
            new Trigger\ApplicationContext('Production/Standby'),
        ],
        indicators: [
            new Indicator\Backend\Toolbar([
                'color' => '#2f9c91',
            ]),
        ],
    );
