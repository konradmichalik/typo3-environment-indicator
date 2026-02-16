..  include:: /Includes.rst.txt

..  _backend-theme:

=======================
Backend theme
=======================

..  versionadded:: 3.x

    This indicator requires TYPO3 v14 or later.

The backend theme indicator colors the entire TYPO3 backend by overriding
CSS custom properties introduced with the TYPO3 v14 theming system. Instead of
modifying individual elements, it sets the primary accent color and scaffold
variables, which automatically cascade through the backend UI - including the
topbar, sidebar, buttons, active states, and focus rings.

This works with all TYPO3 backend themes (Fresh, Modern, Classic) and supports
both light and dark mode.

You can adjust the theme in your :code:`ext_localconf.php`:

..  code-block:: php
    :caption: ext_localconf.php

    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Handler;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger;

    Handler::addIndicator(
        triggers: [
            new Trigger\ApplicationContext('Development*'),
        ],
        indicators: [
            new Indicator\Backend\Theme([
                'color' => '#bd593a',
            ]),
        ],
    );

Configuration keys:

- :code:`color` (string): The primary accent color as hex value. **Required.**
- :code:`scaffoldHeader` (bool): Whether to color the backend header. Default is :code:`true`.
- :code:`scaffoldSidebar` (bool): Whether to color the backend sidebar. Default is :code:`true`.
- :code:`neutralMix` (string): The neutral tinting strength as CSS percentage value. Default is :code:`'15%'`.

..  code-block:: php
    :caption: ext_localconf.php - Full configuration example

    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Handler;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger;

    Handler::addIndicator(
        triggers: [
            new Trigger\ApplicationContext('Development*'),
        ],
        indicators: [
            new Indicator\Backend\Theme([
                'color' => '#bd593a',
                'scaffoldHeader' => true,
                'scaffoldSidebar' => true,
                'neutralMix' => '20%',
            ]),
        ],
    );

..  note::
    This indicator requires TYPO3 v14 or later. On TYPO3 v13 and below, the
    indicator is silently ignored. The TYPO3 backend CSS framework is currently
    considered internal API. While the overridden CSS custom properties are
    stable within TYPO3 v14, they may change in future major versions.

..  note::
    The backend theme indicator can be combined with other backend indicators
    like :ref:`Backend toolbar <backend-toolbar-item>` or
    :ref:`Backend topbar <backend-topbar>` for maximum visual distinction
    between environments.
