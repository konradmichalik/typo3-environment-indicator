..  include:: /Includes.rst.txt

..  image:: /Images/Extension-EI-PageTitlePrefix.png
    :alt: Page Title Icon
    :width: 120px

..  _page-title:

===========
Page Title
===========

The favicon indicator fails exactly where editors and developers work a lot:
pinned tabs (favicon too small or hidden), bookmarks, browser history and task
switchers. A prefix or suffix in the :code:`<title>` — e.g.
:code:`[STAGING] Page title` — makes the environment instantly recognizable in
all of these contexts. It is a perfect complement to the favicon indicator.

..  figure:: /Images/preview-page-title-prefix.jpg
    :alt: Page Title Prefix Example

    Page Title Prefix Example

The indicator works in both frontend and backend:

- **Frontend:** a middleware decorates the final :code:`<title>` after the
  PageTitle API has resolved, so all existing page title providers (including
  SEO extensions) remain untouched.
- **Backend:** the document title is prefixed/suffixed client-side. A
  :code:`MutationObserver` re-applies the decoration on backend SPA navigation
  (module switches), so the prefix persists.

You can register the indicator in your :code:`ext_localconf.php`:

..  code-block:: php
    :caption: ext_localconf.php

    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Handler;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger;

    Handler::addIndicator(
        triggers: [
            new Trigger\ApplicationContext('Production/Staging')
        ],
        indicators: [
            new Indicator\General\PageTitle([
                'prefix' => '[%context%] ',
            ])
        ]
    );

Additional optional configuration keys:

- :code:`prefix` (string): Prepended to the page title (e.g. :code:`[STAGING] `).
  Default is :code:`[%context%] `. The placeholder :code:`%context%` is replaced
  with the current application context.
- :code:`suffix` (string): Appended to the page title. Also supports the
  :code:`%context%` placeholder. Default is empty.

..  note::
    The title is only decorated once. A title that already starts with the
    prefix (or ends with the suffix) is left untouched — safe for a shared
    full-page cache.

..  note::
    SEO extensions that also manipulate the title are unaffected: the frontend
    decoration runs on the final rendered title, after the PageTitle API. Since
    the indicators are not active in production anyway, the risk of interference
    is limited.

The page title indicator can be disabled per context via the
:ref:`extension configuration <extconf-frontend.pageTitle>`
(:code:`frontend.pageTitle` and :code:`backend.pageTitle`).
