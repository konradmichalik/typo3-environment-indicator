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

Processes a favicon path through the configured environment indicator modifiers. If no :ref:`Favicon indicator <favicon>` is active for the current context, the original path is returned unchanged. The same happens if the feature is switched off in the :ref:`extension configuration <extension-configuration>` or if the image processing fails.

The favicon path is the content of the ViewHelper. The registered :code:`favicon` argument is not evaluated, pass the
path as tag content or through the inline pipe.

**Usage:**

..  code-block:: html

    <!-- As tag content -->
    <env:favicon>{f:uri.resource(path:'EXT:your_extension/Resources/Public/Favicon/favicon.png')}</env:favicon>

    <!-- As inline pipe -->
    {f:uri.resource(path:'EXT:your_extension/Resources/Public/Favicon/favicon.png') -> env:favicon()}

..  seealso::

    View the sources on GitHub:

    -   `FaviconViewHelper <https://github.com/konradmichalik/typo3-environment-indicator/blob/main/Classes/ViewHelpers/FaviconViewHelper.php>`__

ImageViewHelper
================

Processes a frontend image path through the configured environment indicator modifiers. If no :ref:`Frontend Image indicator <frontend-image>` is active for the current context, the original path is returned unchanged. The same happens if the feature is switched off in the :ref:`extension configuration <extension-configuration>` or if the image processing fails.

The image path is the content of the ViewHelper. The registered :code:`_path` argument is not evaluated, pass the path
as tag content or through the inline pipe.

**Usage:**

..  code-block:: html

    <!-- As tag content -->
    <env:image>{f:uri.resource(path:'EXT:your_extension/Resources/Public/Images/logo.png')}</env:image>

    <!-- As inline pipe -->
    {f:uri.resource(path:'EXT:your_extension/Resources/Public/Images/logo.png') -> env:image()}

..  seealso::

    View the sources on GitHub:

    -   `ImageViewHelper <https://github.com/konradmichalik/typo3-environment-indicator/blob/main/Classes/ViewHelpers/ImageViewHelper.php>`__
