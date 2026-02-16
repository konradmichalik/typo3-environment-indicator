..  include:: /Includes.rst.txt

..  _viewhelpers:

=============
ViewHelpers
=============

The extension provides two Fluid ViewHelpers for use in custom templates.

**Namespace:** :code:`http://typo3.org/ns/KonradMichalik/Typo3EnvironmentIndicator/ViewHelpers`

**Namespace prefix:** :code:`env`

..  code-block:: html

    <html xmlns:env="http://typo3.org/ns/KonradMichalik/Typo3EnvironmentIndicator/ViewHelpers"
        data-namespace-typo3-fluid="true">

.. contents:: Table of Contents
   :local:
   :depth: 2

FaviconViewHelper
==================

Processes a favicon path through the configured environment indicator modifiers. If no :ref:`Favicon indicator <favicon>` is active for the current context, the original path is returned unchanged.

**Arguments:**

.. list-table::
   :header-rows: 1
   :widths: 15 15 70

   * - Argument
     - Type
     - Description
   * - :code:`favicon`
     - string
     - The favicon path. Can also be passed as tag content (inline notation).

**Usage:**

..  code-block:: html

    <!-- As argument -->
    {env:favicon(favicon:'EXT:your_extension/Resources/Public/Favicon/favicon.png')}

    <!-- As inline pipe -->
    {f:uri.resource(path:'EXT:your_extension/Resources/Public/Favicon/favicon.png') -> env:favicon()}

..  seealso::

    View the sources on GitHub:

    -   `FaviconViewHelper <https://github.com/konradmichalik/typo3-environment-indicator/blob/main/Classes/ViewHelpers/FaviconViewHelper.php>`__

ImageViewHelper
================

Processes a frontend image path through the configured environment indicator modifiers. If no :ref:`Frontend Image indicator <frontend-image>` is active for the current context, the original path is returned unchanged.

**Arguments:**

.. list-table::
   :header-rows: 1
   :widths: 15 15 70

   * - Argument
     - Type
     - Description
   * - :code:`_path`
     - string
     - The image path. Can also be passed as tag content (inline notation).

**Usage:**

..  code-block:: html

    <!-- As inline pipe -->
    {f:uri.resource(path:'EXT:your_extension/Resources/Public/Images/logo.png') -> env:image()}

..  seealso::

    View the sources on GitHub:

    -   `ImageViewHelper <https://github.com/konradmichalik/typo3-environment-indicator/blob/main/Classes/ViewHelpers/ImageViewHelper.php>`__
