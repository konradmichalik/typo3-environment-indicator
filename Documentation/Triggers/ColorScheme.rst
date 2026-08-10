..  include:: /Includes.rst.txt

..  _trigger-colorscheme:

============
Color Scheme
============

..  versionadded:: 3.5

    Requires TYPO3 v13.3 or later, where the backend color scheme switch was introduced.

The :code:`ColorScheme` trigger is used to show the indicators depending on the color scheme (:code:`light`, :code:`dark` or :code:`auto`) the backend user has selected.

This is mainly useful for image-based indicators such as :ref:`backend-logo` or :ref:`favicon`. Those indicators are rendered into a raster image, so a single fixed appearance cannot adapt to the backend theme on its own. Registering one configuration per color scheme produces a separate image for each.

..  code-block:: php
    :caption: ext_localconf.php

    \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Handler::addIndicator(
        triggers: [
            new \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger\ApplicationContext('Development*'),
            new \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger\ColorScheme('light', 'auto')
        ],
        indicators: [
            new \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Backend\Logo([
                new \KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\TextModifier([
                    'text' => 'DEV',
                    'color' => '#000000',
                ]),
            ]),
        ]
    );

    \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Handler::addIndicator(
        triggers: [
            new \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger\ApplicationContext('Development*'),
            new \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger\ColorScheme('dark')
        ],
        indicators: [
            new \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Backend\Logo([
                new \KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\TextModifier([
                    'text' => 'DEV',
                    'color' => '#ffffff',
                ]),
            ]),
        ]
    );

The configuration supports multiple color schemes as separate arguments. Allowed values are :code:`light`, :code:`dark` and :code:`auto`. Any other value will throw an :code:`InvalidArgumentException` during construction.

..  note::

    :code:`auto` is a value of its own and is **not** an alias for :code:`light`. It is the TYPO3 default for users who never touched the color scheme switch, so a configuration that should also apply to them has to list it explicitly, as in :code:`ColorScheme('light', 'auto')`.

    Because :code:`auto` is resolved by the browser rather than the server, an indicator registered for it cannot follow the operating system setting. It always renders the one variant that was generated for it.

..  note::

    Switching the color scheme does not update image-based indicators right away — they change with the next full page load.

    TYPO3 applies the switch on the client by toggling CSS only; it neither re-renders the topbar nor requests a new logo. The generated image is referenced as :code:`<img src="...">` from markup that was rendered server-side, so it keeps pointing at the previous variant until the page is requested again.

..  note::

    The backend theme :code:`classic` renders the topbar with :code:`color-scheme: only dark`, so its header stays dark regardless of the selected color scheme. A configuration registered for :code:`light` therefore appears on a dark header for those users. The backend theme is a separate user setting and is not evaluated by this trigger.

..  note::

    The trigger requires a backend user and therefore never applies in the frontend.
