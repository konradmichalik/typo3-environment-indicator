..  include:: /Includes.rst.txt

..  image:: /Images/Extension-EI-BackendTheme.png
    :alt: Backend Theme Icon
    :width: 120px

..  _backend-theme:

=======================
Backend theme
=======================

..  versionadded:: 3.1

    This indicator requires TYPO3 v14 or later.

..  warning::
    This feature is **experimental**. The TYPO3 backend CSS framework is
    currently considered internal API. While the overridden CSS custom properties
    are stable within TYPO3 v14, they may change in future major versions.

..  note::
    Because it is experimental, the backend theme is **disabled by default**.
    Enable it via the extension configuration setting :code:`backend.theme`
    (:guilabel:`Admin Tools > Settings > Extension Configuration`). Even with the
    default configuration active, the backend stays uncolored until this flag is
    turned on.

..  figure:: /Images/theme.jpg
    :alt: Backend theme indicator preview

The backend theme indicator colors the entire TYPO3 backend by overriding
CSS custom properties introduced with the
`TYPO3 v14 Fresh theme <https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.0/Feature-108240-IntroduceFreshTheme.html>`__.
Instead of modifying individual elements, it sets the primary accent color and
scaffold variables, which automatically cascade through the backend UI -
including the topbar, sidebar, buttons, active states, and focus rings.

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
    indicator is silently ignored.

..  important::
    When this indicator is active, the user's theme selection in
    :guilabel:`User Settings > Theme` is overridden by the environment color.
    An info box is displayed in the User Settings to inform the user.

..  note::
    The backend theme indicator can be combined with other backend indicators
    like :ref:`Backend toolbar <backend-toolbar-item>` or
    :ref:`Backend topbar <backend-topbar>` for maximum visual distinction
    between environments.
