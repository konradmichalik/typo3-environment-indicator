..  include:: /Includes.rst.txt

..  image:: /Images/Extension-EI-CLI.png
    :alt: CLI Banner Icon
    :width: 120px

..  _cli-banner:

===========
CLI Banner
===========

The classic "wrong SSH terminal" scenario is the CLI counterpart to the original
motivation of this extension: running an import, a custom deployment command or
:code:`impexp:import` on the live system instead of staging. The CLI banner
prints a colored environment banner to :code:`stderr` before a console command
runs, e.g. :code:`🚦 STAGING — project.example.dev`.

The banner is printed via a listener on the Symfony Console
:php:`ConsoleCommandEvent`, so it appears before the command is executed. It is
written to :code:`stderr`, so parsed :code:`stdout` output (JSON, CSV) stays
untouched.

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
            new Indicator\Cli\Banner([
                'text' => 'STAGING',
                'color' => 'cyan',
            ])
        ]
    );

Additional optional configuration keys:

- :code:`text` (string): The banner text. Default is the current application
  context.
- :code:`color` (string): The banner color. Accepts a named ANSI color
  (e.g. :code:`cyan`, :code:`red`, :code:`yellow`) or a hex value
  (e.g. :code:`#00ACC1`, rendered on truecolor terminals). Default is no color.
- :code:`icon` (string): A leading glyph. Default is :code:`🚦`.
- :code:`commands` (array): A whitelist of command name patterns (:code:`fnmatch`
  syntax, e.g. :code:`['site:*', 'impexp:*']`). If set, the banner is only
  printed for matching commands. Default is empty (the banner is printed for all
  commands).

..  note::
    The banner is only printed for interactive terminals. Cron jobs, CI
    pipelines and scheduler runs (as well as :code:`--no-interaction`) are left
    untouched to avoid log noise. Non-decorated output (piped :code:`stdout`,
    the :code:`NO_COLOR` convention) automatically drops the ANSI color codes.

..  note::
    In the CLI there is no backend/frontend user and no client IP. User- and
    IP-based triggers (:ref:`trigger-admin`, :ref:`trigger-backend-user-groups`,
    :ref:`trigger-frontend-user-groups`, :ref:`trigger-ip`) therefore cannot
    resolve and the banner will not activate. :ref:`trigger-application-context`
    and :ref:`trigger-custom` work as usual.

..  note::
    This indicator is deliberately *indicator only* (a banner). It does not add
    confirmation prompts ("Really execute on live? [y/N]") — that would be guard
    functionality and out of scope.

..  note::
    A small set of low-level TYPO3 core commands (:code:`cache:flush`,
    :code:`cache:warmup`, :code:`cache:flushtags`, :code:`list`, :code:`help`,
    :code:`upgrade:*`, among others) always run in TYPO3's failsafe CLI
    bootstrap, which uses a no-op event dispatcher for resilience — so this and
    any other third-party listener never fires for them, regardless of
    configuration. Some community packages register additional commands the
    same way (e.g. :code:`helhum/typo3-console`'s :code:`database:updateschema`,
    :code:`database:import`, :code:`install:*`). The banner works normally for
    everything else, including custom extension commands, :code:`site:*` and
    :code:`impexp:*`.

The CLI banner can be disabled globally via the
:ref:`extension configuration <extconf-cli.banner>`.
