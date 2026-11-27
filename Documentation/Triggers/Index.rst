..  include:: /Includes.rst.txt

..  _triggers:

============
Triggers
============

Triggers act as conditions and are used to define when environment indicators should be shown.

You can combine multiple different triggers within a single :code:`Handler::addIndicator()` call. All triggers must return :code:`true` (AND logic) for the associated indicators to activate.

..  code-block:: php
    :caption: ext_localconf.php

    \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Handler::addIndicator(
        triggers: [
            new \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger\ApplicationContext('Development'),
            new \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger\Admin()
        ],
        indicators: [
            new \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Backend\Topbar([
                'color' => '#00ACC1',
            ]),
        ]
    );

There are several triggers available, which can be used to show the indicators in different contexts.

..  _trigger-page-cache:

Triggers and the page cache
***************************

TYPO3 caches rendered frontend pages. The indicators are evaluated at different times, which matters for triggers that
depend on the request or on the user.

..  list-table::
    :header-rows: 1
    :widths: 30 70

    *   -   Evaluated
        -   Indicators
    *   -   On every request
        -   :ref:`Frontend Hint <frontend-hint>`, :ref:`Console <console>`, :ref:`HTTP Header <frontend-http-header>`,
            :ref:`Robots <frontend-robots>`, :ref:`Page Title <page-title>`
    *   -   Once per cache entry
        -   The frontend :ref:`Favicon <favicon>` (:code:`page.shortcutIcon` and :code:`FaviconViewHelper`) and the
            :ref:`Frontend Image <frontend-image>`

The HTTP header, robots and page title indicators are applied to the response after the page was taken from the cache.
The hint and the console are rendered through TypoScript conditions, whose result is part of the page cache
identifier, so each outcome gets its own cache entry.

The frontend favicon and the frontend image end up in the cached page HTML. Together with a trigger that varies per
visitor, the first visitor decides what every later visitor gets from the same cache entry. This applies to
:code:`Ip`, to :code:`Admin`, :code:`BackendUserGroup`, :code:`Theme` and :code:`ColorScheme` (these depend on a backend
login) and to :code:`Custom` if its function reads the request or the user. Combine the frontend favicon and the
frontend image with triggers that do not vary per visitor, such as :code:`ApplicationContext`.

The :code:`FrontendUserGroup` trigger is not affected, because TYPO3 includes the frontend user groups in the page cache
identifier.


..  toctree::
    :maxdepth: 3

    Admin
    ApplicationContext
    BackendUserGroups
    ColorScheme
    Custom
    FrontendUserGroups
    Ip
    Theme
