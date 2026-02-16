..  include:: /Includes.rst.txt

..  _custom-modifiers:

=======================
Custom modifiers
=======================

The predefined modifiers are not enough for your use case? No problem! You can easily write your own.

Implement your own image modifier by extending the :code:`AbstractModifier` class and implementing the :code:`ModifierInterface` method.

..  code-block:: php
    :caption: Classes/Image/CustomModifier.php

    <?php

    namespace Vendor\YourExt\Image\Modifier;

    use Intervention\Image\Geometry\Factories\RectangleFactory;
    use Intervention\Image\Interfaces\ImageInterface;
    use KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\AbstractModifier;
    use KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\ModifierInterface;

    class CustomModifier extends AbstractModifier implements ModifierInterface {

        public function modify(ImageInterface &$image): void
        {
            // Modify the image using $this->configuration
        }

        public function validateConfigurationWithErrors(array $configuration): array
        {
            $errors = [];

            if (!isset($configuration['myRequiredKey'])) {
                $errors[] = 'Missing required configuration key: myRequiredKey';
            }

            return [
                'valid' => [] === $errors,
                'errors' => $errors,
            ];
        }
    }

..  seealso::

    View the sources on GitHub:

    -   `AbstractModifier <https://github.com/konradmichalik/typo3-environment-indicator/blob/main/Classes/Image/Modifier/AbstractModifier.php>`__
    -   `ModifierInterface <https://github.com/konradmichalik/typo3-environment-indicator/blob/main/Classes/Image/Modifier/ModifierInterface.php>`__

See the `Intervention Image documentation <http://image.intervention.io/v3>`_ for more information about image
manipulation.

..  note::
    Having fun with colorful favicons? Use the :code:`ColorUtility::getColoredString()`
    function as color entry in your modifier configuration to generate a color based on a string (default is the
    :code:`$GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename']`).
