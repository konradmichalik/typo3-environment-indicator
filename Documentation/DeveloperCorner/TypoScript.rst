..  include:: /Includes.rst.txt

..  _typoscript-reference:

======================
TypoScript reference
======================

The extension provides TypoScript objects and two condition functions that can be used in custom TypoScript configurations.

.. contents:: Table of Contents
   :local:
   :depth: 2

Condition: enableTechnicalContext()
====================================

The :code:`enableTechnicalContext()` condition function evaluates whether the frontend hint indicator is active for the current environment. It returns :code:`true` when the frontend context feature is enabled in the extension configuration **and** a :code:`Frontend\Hint` indicator is configured for the current application context. The function is provided by :code:`TechnicalContextConditionFunctionsProvider`.

..  code-block:: typoscript

    [enableTechnicalContext()]
    page.10 = TEXT
    page.10.value = Environment indicator is active
    [GLOBAL]

TypoScript objects
===================

The following :code:`lib.*` objects are registered within the :code:`[enableTechnicalContext()]` condition block via the included Site Set. They can be used or overridden in your own TypoScript.

.. list-table::
   :header-rows: 1
   :widths: 35 65

   * - Object
     - Description
   * - :code:`lib.applicationContextTitle`
     - Returns the current TYPO3 application context (e.g. :code:`Development/DDEV`).
   * - :code:`lib.applicationContextColor`
     - Returns the configured color of the :ref:`Frontend Hint <frontend-hint>` indicator.
   * - :code:`lib.applicationContextTextColor`
     - Returns the optimal text color (black or white) for the configured hint color.
   * - :code:`lib.websiteTitle`
     - Returns the :code:`text` option of the Frontend Hint if set. Otherwise it returns the website title from the site configuration, then the site identifier.
   * - :code:`lib.applicationContextDescription`
     - Returns the :code:`description` option of the Frontend Hint.
   * - :code:`lib.applicationContextPositionClass`
     - Returns the CSS class for the position of the hint (e.g. :code:`technical-context--top-left`).
   * - :code:`lib.technicalContext`
     - A :code:`FLUIDTEMPLATE` that renders the complete frontend hint element. This object is automatically added to :code:`page` output.

Condition: enableEnvironmentConsole()
======================================

The :code:`enableEnvironmentConsole()` condition function returns :code:`true` when the :code:`frontend.console`
feature is enabled in the extension configuration **and** a :code:`General\Console` indicator is configured for the
current application context. Within this condition the following objects are registered:

.. list-table::
   :header-rows: 1
   :widths: 35 65

   * - Object
     - Description
   * - :code:`lib.environmentConsoleBadgeText`
     - Returns the text of the console badge.
   * - :code:`lib.environmentConsoleBadgeStyle`
     - Returns the CSS style of the console badge.
   * - :code:`lib.environmentConsole`
     - A :code:`FLUIDTEMPLATE` that renders the console script. This object is automatically added to :code:`page` output.

Overriding objects
===================

You can override any of these objects in your own TypoScript setup:

..  code-block:: typoscript

    [enableTechnicalContext()]
    lib.applicationContextTitle = TEXT
    lib.applicationContextTitle.value = My Custom Title
    [GLOBAL]
