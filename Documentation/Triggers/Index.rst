..  include:: /Includes.rst.txt

..  _triggers:

============
Triggers
============

Triggers act as conditions and are used to define when environment indicators should be shown.

You can combine multiple different triggers within a single :code:`Handler::addIndicator()` call. All triggers must return :code:`true` (AND logic) for the associated indicators to activate.

..  code-block:: php
    :caption: ext_localconf.php

    \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Handler::addIndicator(
        triggers: [
            new \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger\ApplicationContext('Development'),
            new \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger\Admin()
        ],
        indicators: [
            new \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Backend\Topbar([
                'color' => '#00ACC1',
            ]),
        ]
    );

There are several triggers available, which can be used to show the indicators in different contexts.


..  toctree::
    :maxdepth: 3

    Admin
    ApplicationContext
    BackendUserGroups
    ColorScheme
    Custom
    FrontendUserGroups
    Ip
    Theme
