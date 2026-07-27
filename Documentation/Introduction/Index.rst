..  include:: /Includes.rst.txt

..  _introduction:

============
Introduction
============

..  _what-it-does:

What does it do?
================

This extension provides several features to show an environment indicator in the TYPO3 frontend and backend.


..  note::
    These environment indicators are mainly for development purposes (e.g. distinguishing between different test systems) and will not show in production environments.

..  _features:

Features
========

Combine :ref:`trigger <triggers>` and :ref:`indicator <indicators>` classes to show the environment in the TYPO3 frontend and backend. The extension provides several trigger and indicator classes out of the box. You can also create your own trigger and indicator classes.

.. figure:: /Images/example1.jpg
    :alt: Example
    :class: with-shadow

    Backend with several environment indicators

Frontend + Backend
------------------

..  list-table::
    :header-rows: 1
    :widths: 10 30 60
    :class: center

    *   -   Icon
        -   Preview
        -   Description
    *   -   ..  image:: /Images/Extension-EI-Favicon.png
                :alt: Favicon Icon
                :width: 80px
        -   ..  image:: /Images/preview-favicon.png
                :alt: Favicon Preview
        -   :ref:`Modified favicon <favicon>`: Modifies the favicon for frontend and backend based on the original favicon, current environment and your configuration.
    *   -   ..  image:: /Images/Extension-EI-PageTitlePrefix.png
                :alt: Page Title Icon
                :width: 80px
        -   ..  image:: /Images/preview-page-title-prefix.jpg
                :alt: Page Title Preview
        -   :ref:`Page title <page-title>`: Prefix or suffix the page title in frontend and backend with the current application context.

Frontend
--------

..  list-table::
    :header-rows: 1
    :widths: 10 30 60
    :class: center

    *   -   Icon
        -   Preview
        -   Description
    *   -   ..  image:: /Images/Extension-EI-FrontendHint.png
                :alt: Frontend Hint Icon
                :width: 80px
        -   ..  image:: /Images/preview-frontend-hint.png
                :alt: Frontend Hint Preview
        -   :ref:`Frontend Hint <frontend-hint>`: Adds an informative hint to the frontend showing the website title and current environment.
    *   -   ..  image:: /Images/Extension-EI-FrontendImage.png
                :alt: Frontend Image Icon
                :width: 80px
        -   ..  image:: /Images/preview-frontend-image.jpg
                :alt: Frontend Image Preview
        -   :ref:`Modified frontend image <frontend-image>`: Modifies the frontend image based on the original image, current environment and your configuration.

Backend
-------

..  list-table::
    :header-rows: 1
    :widths: 10 30 60
    :class: center

    *   -   Icon
        -   Preview
        -   Description
    *   -   ..  image:: /Images/Extension-EI-BackendToolbarItem.png
                :alt: Backend Toolbar Item Icon
                :width: 80px
        -   ..  image:: /Images/preview-backend-toolbar-item.png
                :alt: Backend Toolbar Item Preview
        -   :ref:`Backend toolbar item <backend-toolbar-item>`: Adds an informative item to the backend toolbar showing the current environment.
    *   -   ..  image:: /Images/Extension-EI-BackendTopbar.png
                :alt: Backend Topbar Icon
                :width: 80px
        -   ..  image:: /Images/preview-backend-topbar.jpg
                :alt: Backend Topbar Preview
        -   :ref:`Backend topbar <backend-topbar>`: Colors the backend header topbar according to the environment.
    *   -   ..  image:: /Images/Extension-EI-BackendLogo.png
                :alt: Backend Logo Icon
                :width: 80px
        -   ..  image:: /Images/preview-backend-logo.jpg
                :alt: Backend Logo Preview
        -   :ref:`Modified backend logo <backend-logo>`: Modifies the backend logo based on the original logo, current environment and your configuration.
    *   -   ..  image:: /Images/Extension-EI-DashboardWidget.png
                :alt: Dashboard Widget Icon
                :width: 80px
        -   ..  image:: /Images/preview-dashboard-widget.jpg
                :alt: Dashboard Widget Preview
        -   :ref:`Dashboard widget <dashboard-widget>`: Render a dashboard widget according to the environment.
    *   -   ..  image:: /Images/Extension-EI-BackendTheme.png
                :alt: Backend Theme Icon
                :width: 80px
        -   ..  image:: /Images/theme.jpg
                :alt: Backend Theme Preview
        -   :ref:`Backend theme <backend-theme>` *(experimental)*: Colors the entire TYPO3 v14+ backend (primary color, header, sidebar) based on the environment.

..  _support:

Support
=======

There are several ways to get support for this extension:

* GitHub: https://github.com/konradmichalik/typo3-environment-indicator/issues

License
=======

This extension is licensed under
`GNU General Public License 2.0 (or later) <https://www.gnu.org/licenses/old-licenses/gpl-2.0.html>`_.
