..  include:: /Includes.rst.txt

..  _favicon:

=======================
Favicon
=======================

The favicon of the frontend or backend context will be modified regarding the environment and the associated
configuration.

..  figure:: /Images/preview-favicon.png
    :alt: Favicon Example

    Favicon Example

.. contents:: Table of Contents
   :local:
   :depth: 2

Frontend
**********

For the **frontend**, the original favicon will either be fetched from the typoscript configuration
:code:`page.shortcutIcon` (see `typoscript reference <https://docs.typo3.org/m/typo3/reference-typoscript/main/en-us/Guide/Page/Index.html#guide-page-favicon>`_)
or can be handled by your own fluid template via the :code:`FaviconViewHelper`:


..  code-block:: html
    :caption: Custom fluid template

    <html xmlns:env="http://typo3.org/ns/KonradMichalik/Typo3EnvironmentIndicator/ViewHelpers"
        data-namespace-typo3-fluid="true">

    {f:uri.resource(path:'EXT:your_extension/Resources/Public/Favicon/favicon.png') -> env:favicon()}
    {env:favicon(favicon:'EXT:your_extension/Resources/Public/Favicon/favicon.png')}

..  seealso::

    View the sources on GitHub:

    -   `FaviconViewHelper <https://github.com/konradmichalik/typo3-environment-indicator/blob/main/Classes/ViewHelpers/FaviconViewHelper.php>`__

Backend
**********

For the **backend**, the favicon will be fetched by the extension configuration of
:code:`$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['backend']['backendFavicon']`.

..  figure:: /Images/favicon-examples.png
    :alt: Favicon Examples

    Favicon Examples

..  _favicon-scope:

Scope
**********

By default, the :code:`Favicon` indicator applies modifications to both frontend and backend. You can restrict it to a specific application type using the :code:`Scope` enum:

..  code-block:: php
    :caption: ext_localconf.php

    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Handler;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger;
    use KonradMichalik\Typo3EnvironmentIndicator\Enum\Scope;
    use KonradMichalik\Typo3EnvironmentIndicator\Image;

    Handler::addIndicator(
        triggers: [
            new Trigger\ApplicationContext('Development*'),
        ],
        indicators: [
            // Only modify the frontend favicon
            new Indicator\Favicon(
                configuration: [
                    new Image\Modifier\TextModifier([
                        'text' => 'DEV',
                        'color' => '#bd593a',
                    ]),
                ],
                scope: Scope::Frontend,
            ),
        ],
    );

Available scope values:

- :code:`Scope::Global` (default): Apply to both frontend and backend.
- :code:`Scope::Frontend`: Apply only to the frontend.
- :code:`Scope::Backend`: Apply only to the backend.

..  _favicon-modifiers:

Modifiers
**********

The favicon modification configuration can be found in
:code:`$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['typo3_environment_indicator']`.

Add a configured favicon modifier to the desired environment (e.g. :code:`Testing`) in your :code:`ext_localconf.php`:

..  code-block:: php
    :caption: ext_localconf.php

    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Handler;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger;
    use KonradMichalik\Typo3EnvironmentIndicator\Image;

    Handler::addIndicator(
        triggers: [
            new Trigger\ApplicationContext('Testing')
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
                ])
            ])
        ]
    );

..  figure:: /Images/Favicons/typo3-test.png
    :alt: Favicon Modifier Example


The modifiers will be executed one after the other. You can combine them if you want.

The following modifier classes are available:

TextModifier
===========

This is the default modifier if no own configuration is set.

..  code-block:: php
    :caption: ext_localconf.php

    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Handler;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger;
    use KonradMichalik\Typo3EnvironmentIndicator\Image;

    Handler::addIndicator(
        triggers: [
            new Trigger\ApplicationContext('Development')
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
                ])
            ])
        ]
    );


..  figure:: /Images/Favicons/typo3-text.png
    :alt: Favicon TextModifier Example


Additional optional configuration keys:

- :code:`font` (string): The font file path for the text. Default is :code:`EXT:typo3_environment_indicator/Resources/Public/Fonts/OpenSans-Bold.ttf`.
- :code:`position` (string): The position of the text. Default is :code:`top`. Possible values are :code:`bottom`, :code:`top`.
- :code:`stroke` (array): Optional stroke configuration for the text outline. If set, it requires the sub-keys :code:`color` (string) and :code:`width` (numeric).

..  seealso::

    View the sources on GitHub:

    -   `TextModifier <https://github.com/konradmichalik/typo3-environment-indicator/blob/main/Classes/Image/Modifier/TextModifier.php>`__

TriangleModifier
===========

Adds a triangle indicator to the favicon.


..  code-block:: php
    :caption: ext_localconf.php

    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Handler;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger;
    use KonradMichalik\Typo3EnvironmentIndicator\Image;

    Handler::addIndicator(
        triggers: [
            new Trigger\ApplicationContext('Development')
        ],
        indicators: [
            new Indicator\Favicon([
                new Image\Modifier\TriangleModifier([
                    'color' => '#bd593a',
                ])
            ])
        ]
    );

..  figure:: /Images/Favicons/typo3-triangle.png
    :alt: Favicon TriangleModifier Example


Additional optional configuration keys:

- :code:`size` (float): The percentage size of the triangle. Default is :code:`0.7`.
- :code:`position` (string): The position of the triangle. Default is :code:`bottom right`. Possible values are :code:`bottom left`, :code:`bottom right`, :code:`top left`, :code:`top right`.

..  seealso::

    View the sources on GitHub:

    -   `TriangleModifier <https://github.com/konradmichalik/typo3-environment-indicator/blob/main/Classes/Image/Modifier/TriangleModifier.php>`__

CircleModifier
===========

Adds a circle indicator to the favicon.

..  code-block:: php
    :caption: ext_localconf.php

    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Handler;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger;
    use KonradMichalik\Typo3EnvironmentIndicator\Image;

    Handler::addIndicator(
        triggers: [
            new Trigger\ApplicationContext('Development')
        ],
        indicators: [
            new Indicator\Favicon([
                new Image\Modifier\CircleModifier([
                    'color' => '#bd593a',
                ])
            ])
        ]
    );

..  figure:: /Images/Favicons/typo3-circle.png
    :alt: Favicon CircleModifier Example


Additional optional configuration keys:

- :code:`size` (float): The percentage size of the circle. Default is :code:`0.4`.
- :code:`position` (string): The position of the circle. Default is :code:`bottom right`. Possible values are :code:`top left`, :code:`top center`, :code:`top right`, :code:`center left`, :code:`center`, :code:`center right`, :code:`bottom left`, :code:`bottom center`, :code:`bottom right`.
- :code:`padding` (float): The percentage padding of the circle. Default is :code:`0.1`.

..  seealso::

    View the sources on GitHub:

    -   `CircleModifier <https://github.com/konradmichalik/typo3-environment-indicator/blob/main/Classes/Image/Modifier/CircleModifier.php>`__

FrameModifier
===========

Adds a frame around the favicon.

..  code-block:: php
    :caption: ext_localconf.php

    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Handler;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger;
    use KonradMichalik\Typo3EnvironmentIndicator\Image;

    Handler::addIndicator(
        triggers: [
            new Trigger\ApplicationContext('Development')
        ],
        indicators: [
            new Indicator\Favicon([
                new Image\Modifier\FrameModifier([
                    'color' => '#bd593a',
                ])
            ])
        ]
    );

..  figure:: /Images/Favicons/typo3-frame.png
    :alt: Favicon FrameModifier Example


Additional optional configuration keys:

- :code:`borderSize` (float): The border size of the frame. Default is :code:`5`.

..  seealso::

    View the sources on GitHub:

    -   `FrameModifier <https://github.com/konradmichalik/typo3-environment-indicator/blob/main/Classes/Image/Modifier/FrameModifier.php>`__

ReplaceModifier
===========

Replace the original favicon with a custom one regarding the environment.

..  code-block:: php
    :caption: ext_localconf.php

    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Handler;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger;
    use KonradMichalik\Typo3EnvironmentIndicator\Image;

    Handler::addIndicator(
        triggers: [
            new Trigger\ApplicationContext('Development')
        ],
        indicators: [
            new Indicator\Favicon([
                new Image\Modifier\ReplaceModifier([
                    'path' => 'EXT:sitepackage/Resources/Public/Icons/favicon.png',
                ])
            ])
        ]
    );

..  figure:: /Images/Favicons/replace.png
    :alt: Favicon ReplaceModifier Example

..  seealso::

    View the sources on GitHub:

    -   `ReplaceModifier <https://github.com/konradmichalik/typo3-environment-indicator/blob/main/Classes/Image/Modifier/ReplaceModifier.php>`__

OverlayModifier
===========

Overlay an additional image to the original favicon regarding the environment.

..  code-block:: php
    :caption: ext_localconf.php

    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Handler;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger;
    use KonradMichalik\Typo3EnvironmentIndicator\Image;

    Handler::addIndicator(
        triggers: [
            new Trigger\ApplicationContext('Development')
        ],
        indicators: [
            new Indicator\Favicon([
                new Image\Modifier\OverlayModifier([
                    'path' => 'EXT:sitepackage/Resources/Public/Icons/favicon.png',
                ])
            ])
        ]
    );

..  figure:: /Images/Favicons/typo3-overlay.png
    :alt: Favicon OverlayModifier Example


Additional optional configuration keys:

- :code:`size` (float): The percentage size of the overlay. Default is :code:`0.5`.
- :code:`position` (string): The position of the overlay. Default is :code:`bottom right`. Possible values are :code:`top left`, :code:`top center`, :code:`top right`, :code:`center left`, :code:`center`, :code:`center right`, :code:`bottom left`, :code:`bottom center`, :code:`bottom right`.
- :code:`padding` (float): The percentage padding of the overlay. Default is :code:`0.1`.

..  seealso::

    View the sources on GitHub:

    -   `OverlayModifier <https://github.com/konradmichalik/typo3-environment-indicator/blob/main/Classes/Image/Modifier/OverlayModifier.php>`__

ColorizeModifier
===========

Colorize the original favicon with a specific color regarding the environment.

..  warning::
    This modifier is only available with "Imagick" image driver.

..  code-block:: php
    :caption: ext_localconf.php

    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Handler;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger;
    use KonradMichalik\Typo3EnvironmentIndicator\Image;

    Handler::addIndicator(
        triggers: [
            new Trigger\ApplicationContext('Development')
        ],
        indicators: [
            new Indicator\Favicon([
                new Image\Modifier\ColorizeModifier([
                    'color' => '#039BE5',
                ])
            ])
        ]
    );

..  figure:: /Images/Favicons/typo3-colorize.png
    :alt: Favicon ColorizeModifier Example


Additional optional configuration keys:

- :code:`opacity` (float): Controls the opacity of the colorization. Default is :code:`1`.
- :code:`brightness` (integer): Controls the brightness of the colorization. Possible values are from :code:`-100` to :code:`100`.
- :code:`contrast` (integer): Controls the contrast of the colorization. Possible values are from :code:`-100` to :code:`100`.

..  seealso::

    View the sources on GitHub:

    -   `ColorizeModifier <https://github.com/konradmichalik/typo3-environment-indicator/blob/main/Classes/Image/Modifier/ColorizeModifier.php>`__

..  note::
    If you want to modify the image to your own need, implement a :ref:`custom modifier <custom-modifiers>` class and add it to the configuration.
