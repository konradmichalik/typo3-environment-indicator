..  include:: /Includes.rst.txt

..  _indicators:

============
Indicators
============

Indicators are hints about the current environment that are shown discreetly in the frontend and/or backend.


.. figure:: /Images/example1.jpg
    :alt: Example
    :class: with-shadow

    Backend with several environment indicators

You can combine multiple indicators.

..  code-block:: php
    :caption: ext_localconf.php

        \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Handler::addIndicator(
            triggers: [
                new \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger\ApplicationContext('Development'),
            ],
            indicators: [
                new \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Favicon([
                    new \KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\TextModifier([
                        'text' => 'TEST',
                        'color' => '#f39c12',
                        'stroke' => [
                            'color' => '#ffffff',
                            'width' => 3,
                        ],
                    ])
                ]),
                new \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Frontend\Hint([
                    'color' => '#FFF176',
                ]),
                new \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Backend\Topbar([
                    'color' => '#00ACC1',
                ]),
            ]
        );


The following indicators are available:

..  toctree::
    :maxdepth: 3

    FrontendHint
    FrontendImage
    BackendToolbar
    BackendTopbar
    BackendTheme
    Favicon
    BackendLogo
    DashboardWidget
