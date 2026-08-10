..  include:: /Includes.rst.txt

..  _trigger-theme:

=====
Theme
=====

..  versionadded:: 3.5

    Requires TYPO3 v13.4 or later, where the :code:`classic` backend theme was introduced.

The :code:`Theme` trigger is used to show the indicators depending on the backend theme (:code:`fresh`, :code:`modern` or :code:`classic`) the backend user has selected.

..  note::

    :code:`Trigger\\Theme` is unrelated to :ref:`backend-theme`. The trigger reads the user's selected backend theme as a condition; the indicator overrides backend CSS custom properties.

This is mainly useful together with :ref:`trigger-colorscheme`. The backend theme and the color scheme are independent user settings, and they do not treat each other equally: the :code:`classic` theme renders the topbar with :code:`color-scheme: only dark`, so its header stays dark no matter which color scheme is selected. An image based indicator registered only for the light color scheme would then appear on a dark header for those users.

..  code-block:: php
    :caption: ext_localconf.php

    \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Handler::addIndicator(
        triggers: [
            new \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger\ApplicationContext('Development*'),
            new \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger\ColorScheme('light', 'auto'),
            new \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger\Theme('classic')
        ],
        indicators: [
            new \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Backend\Logo([
                // light color scheme, but a dark header — so use the light-on-dark variant
                new \KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\TextModifier([
                    'text' => 'DEV',
                    'color' => '#ffffff',
                ]),
            ]),
        ]
    );

The configuration supports multiple themes as separate arguments.

..  note::

    Unlike :ref:`trigger-colorscheme`, the given values are **not** validated against a fixed list. The backend theme is not backed by a core enum, so an unknown theme simply never matches instead of throwing an exception.

..  note::

    A user who never touched the theme switch is on :code:`fresh`, matching the TYPO3 default.

..  note::

    Switching the theme does not update image based indicators right away — they change with the next full page load, for the same reason described in :ref:`trigger-colorscheme`.

..  note::

    The trigger requires a backend user and therefore never applies in the frontend.
