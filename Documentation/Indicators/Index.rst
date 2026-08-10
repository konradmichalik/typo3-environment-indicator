..  include:: /Includes.rst.txt

..  _indicators:

============
Indicators
============

Indicators are hints about the current environment that are shown discreetly in the frontend and/or backend.


.. figure:: /Images/example1.jpg
    :alt: Example
    :class: with-shadow

    Backend with several environment indicators

You can combine multiple indicators.

..  code-block:: php
    :caption: ext_localconf.php

        \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Handler::addIndicator(
            triggers: [
                new \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger\ApplicationContext('Development'),
            ],
            indicators: [
                new \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Favicon([
                    new \KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\TextModifier([
                        'text' => 'TEST',
                        'color' => '#f39c12',
                        'stroke' => [
                            'color' => '#ffffff',
                            'width' => 3,
                        ],
                    ])
                ]),
                new \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Frontend\Hint([
                    'color' => '#FFF176',
                ]),
                new \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Backend\Topbar([
                    'color' => '#00ACC1',
                ]),
            ]
        );


The following indicators are available:

Frontend + Backend
------------------

..  list-table::
    :header-rows: 1
    :widths: 10 30 60

    *   -   Icon
        -   Indicator
        -   Description
    *   -   ..  image:: /Images/Extension-EI-Favicon.png
                :alt: Favicon Icon
                :width: 80px
        -   :ref:`favicon`
        -   Modify the favicon for frontend and backend based on the original favicon, the current application context and your configuration.
    *   -   ..  image:: /Images/Extension-EI-PageTitlePrefix.png
                :alt: Page Title Icon
                :width: 80px
        -   :ref:`page-title`
        -   Prefix or suffix the page title in frontend and backend with the current application context.
    *   -   ..  image:: /Images/Extension-EI-BrowserConsole.png
                :alt: Console Icon
                :width: 80px
        -   :ref:`console`
        -   Print a styled environment badge to the browser console in frontend and backend.

Frontend
--------

..  list-table::
    :header-rows: 1
    :widths: 10 30 60

    *   -   Icon
        -   Indicator
        -   Description
    *   -   ..  image:: /Images/Extension-EI-FrontendHint.png
                :alt: Frontend Hint Icon
                :width: 80px
        -   :ref:`frontend-hint`
        -   Adds an informative hint to the frontend showing the website title and the current application context.
    *   -   ..  image:: /Images/Extension-EI-FrontendImage.png
                :alt: Frontend Image Icon
                :width: 80px
        -   :ref:`frontend-image`
        -   Modify a frontend image based on the original image, the current application context and your configuration.
    *   -   ..  image:: /Images/Extension-EI-HTTPHeader.png
                :alt: Frontend HTTP Header Icon
                :width: 80px
        -   :ref:`frontend-http-header`
        -   Add a configurable HTTP response header with the current application context to frontend responses.
    *   -   ..  image:: /Images/Extension-EI-XRobots.png
                :alt: Frontend Robots Icon
                :width: 80px
        -   :ref:`frontend-robots`
        -   Send an :code:`X-Robots-Tag` header to keep non-production environments out of search engines.
    *   -   ..  image:: /Images/Extension-EI-BrowserConsole.png
                :alt: Frontend Console Icon
                :width: 80px
        -   :ref:`console`
        -   Print a styled environment badge to the browser console on page load.

Backend
-------

..  list-table::
    :header-rows: 1
    :widths: 10 30 60

    *   -   Icon
        -   Indicator
        -   Description
    *   -   ..  image:: /Images/Extension-EI-BackendToolbarItem.png
                :alt: Backend Toolbar Item Icon
                :width: 80px
        -   :ref:`backend-toolbar-item`
        -   Adds an informative item with the current application context to the backend toolbar.
    *   -   ..  image:: /Images/Extension-EI-BackendTopbar.png
                :alt: Backend Topbar Icon
                :width: 80px
        -   :ref:`backend-topbar`
        -   Colorize the backend header topbar regarding the application context.
    *   -   ..  image:: /Images/Extension-EI-BackendLogo.png
                :alt: Backend Logo Icon
                :width: 80px
        -   :ref:`backend-logo`
        -   Modify the backend logo based on the original logo, the current application context and your configuration.
    *   -   ..  image:: /Images/Extension-EI-DashboardWidget.png
                :alt: Dashboard Widget Icon
                :width: 80px
        -   :ref:`dashboard-widget`
        -   Render a dashboard widget according to the environment.
    *   -   ..  image:: /Images/Extension-EI-BackendTheme.png
                :alt: Backend Theme Icon
                :width: 80px
        -   :ref:`backend-theme` *(experimental)*
        -   Colorize the entire TYPO3 v14+ backend (primary color, header, sidebar) based on the environment.
    *   -   ..  image:: /Images/Extension-EI-BackendLogin.png
                :alt: Backend Login Icon
                :width: 80px
        -   :ref:`backend-login`
        -   Show a colored environment badge directly on the backend login screen.

Misc
----

..  list-table::
    :header-rows: 1
    :widths: 10 30 60

    *   -   Icon
        -   Indicator
        -   Description
    *   -   ..  image:: /Images/Extension-EI-CLI.png
                :alt: CLI Banner Icon
                :width: 80px
        -   :ref:`cli-banner`
        -   Print a colored environment banner to stderr before an interactive console command runs.
    *   -   ..  image:: /Images/Extension-EI-MailSubjectPrefix.png
                :alt: Mail Subject Prefix Icon
                :width: 80px
        -   :ref:`mail-subject-prefix`
        -   Prepend the current application context to the subject of every mail sent through the TYPO3 Mailer API.

..  toctree::
    :maxdepth: 3
    :hidden:

    Favicon
    Console
    FrontendHint
    FrontendImage
    FrontendHttpHeader
    FrontendRobots
    Console
    BackendToolbar
    BackendTopbar
    BackendLogo
    DashboardWidget
    BackendTheme
    BackendLogin
    PageTitle
    CliBanner
    MailSubjectPrefix
