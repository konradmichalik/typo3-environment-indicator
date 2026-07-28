..  include:: /Includes.rst.txt

..  _installation:

============
Installation
============

..  _requirements:

Requirements
============

..  list-table:: Version Compatibility
    :header-rows: 1

    * - Version
      - TYPO3
      - PHP
    * - 3.x
      - 13.4 - 14.x
      - 8.2 - 8.5
    * - 2.x
      - 11.5 - 13.4
      - 8.1 - 8.4

Additional requirements:

-   ImageDriver: GD, Imagick or libvips

..  _steps:

Installation
============

Require the extension via Composer (recommended):

..  code-block:: bash

    composer require konradmichalik/typo3-environment-indicator

Or download it from the
`TYPO3 extension repository <https://extensions.typo3.org/extension/typo3_environment_indicator>`__.

..  _migration:

Migration from version 1.x to 2.x
==================================

Since version 2.x, the extension uses the new :php:`Handler::addIndicator()` method to add the
environment indicator configuration instead of the old :php:`ConfigurationUtility::configByContext()`
method.

Configuration
=============

..  note::
    TypoScript configuration is only necessary if you want to use the :ref:`frontend hint <frontend-hint>`.

Site Set (Recommended)
----------------------

..  important::
    Starting with version 3.0, **Site Sets** are required for the frontend TypoScript configuration.
    The legacy static TypoScript include is no longer supported.

Add the site set as a dependency in your site configuration:

..  code-block:: yaml
    :caption: config/sites/<identifier>/config.yaml

    base: 'https://example.com/'
    rootPageId: 1
    dependencies:
      - konradmichalik/typo3-environment-indicator

Static Include (Version 2.x only)
----------------------------------

..  deprecated:: 3.0
    The static TypoScript include is deprecated and removed in version 3.0.
    Please use Site Sets instead.

Alternatively, include the static TypoScript template via the **Template** module
by adding "Environment Indicator" under **Include static (from extensions)**.

Or directly import it in your sitepackage:

..  code-block:: typoscript
    :caption: Configuration/TypoScript/setup.typoscript

    @import 'EXT:typo3_environment_indicator/Configuration/TypoScript/setup.typoscript'

Image Support
============

Only the following favicon image formats are supported: https://image.intervention.io/v3/introduction/formats

* Due to the wide distribution of :code:`.ico` and :code:`.svg` files, these images are also supported.
