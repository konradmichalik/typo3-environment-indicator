..  include:: /Includes.rst.txt

..  _frontend-image:

=======================
Frontend Image
=======================

The frontend image will be modified regarding the environment and the associated
configuration.

..  figure:: /Images/preview-frontend-image.jpg
    :alt: Frontend Image Example

    Frontend Image Example

The image path can be adjusted via the :code:`ImageViewHelper`:


..  code-block:: html
    :caption: Custom fluid template

    <html xmlns:env="http://typo3.org/ns/KonradMichalik/Typo3EnvironmentIndicator/ViewHelpers"
        data-namespace-typo3-fluid="true">

    {f:uri.resource(path:'EXT:your_extension/Resources/Public/Image/Default.png') -> env:image()}
    {env:image(path:'EXT:your_extension/Resources/Public/Image/Default.png')}

..  seealso::

    View the sources on GitHub:

    -   `ImageViewHelper <https://github.com/konradmichalik/typo3-environment-indicator/blob/main/Classes/ViewHelpers/ImageViewHelper.php>`__

Modifiers
**********

The frontend image modification is identical to the :ref:`favicon modification <favicon-modifiers>`. You can use the same modifiers and configuration.

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
            new Indicator\Frontend\Image([
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
    :alt: Frontend Image Modifier Example
