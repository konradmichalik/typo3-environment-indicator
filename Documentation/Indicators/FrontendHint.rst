..  include:: /Includes.rst.txt

..  image:: /Images/Extension-EI-FrontendHint.png
    :alt: Frontend Hint Icon
    :width: 120px

..  _frontend-hint:

=======================
Frontend Hint
=======================

The frontend hint will show the current environment information and the website title as clickable note in the upper right corner.

..  figure:: /Images/frontend-hint.png
    :alt: Frontend hint
    :class: with-shadow

    Frontend hint

You can adjust the color of the hint in your :code:`ext_localconf.php`:

..  code-block:: php
    :caption: ext_localconf.php

    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Handler;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger;

    Handler::addIndicator(
        triggers: [
            new Trigger\ApplicationContext('Testing')
        ],
        indicators: [
            new Indicator\Frontend\Hint([
                'color' => '#bd593a',
            ])
        ]
    );

Additional optional configuration keys:

- :code:`text` (string): The text of the hint. Default is the website title.
- :code:`position` (string): The corner the hint starts in. Default is
  :code:`top left`. Possible values are :code:`top left`, :code:`top right`,
  :code:`bottom left` and :code:`bottom right`. Any other value falls back to
  the default.
- :code:`description` (string): An optional description. When set, it is appended to the hint's tooltip (:code:`title` attribute). Default is empty.

Moving the hint out of the way
==============================

..  versionadded:: 3.5
    Dragging the hint to another corner and closing it for the session were
    added in TYPO3 Environment Indicator 3.5.

..  figure:: /Images/screencast-draggable-frontend-hint.gif
    :alt: Screencast showing the frontend hint being dragged to another corner and closed

    Dragging the hint to another corner and closing it

The hint is positioned relative to the viewport, so it stays visible while
scrolling. If it covers something you need to see, drag it to another corner:
on release it snaps to whichever of the four corners is nearest.

The chosen corner is stored in :code:`localStorage` and takes precedence over
the configured :code:`position` from then on. Because Web Storage is scoped per
origin, every environment remembers its own corner independently.

Closing the hint
================

The close button hides the hint for the current browser session. The state is
stored in :code:`sessionStorage`, so the hint stays gone while navigating the
site and returns in a new browser session.

This is deliberate. The hint is a safety marker that keeps you from mistaking a
staging system for the live site, so it is never dismissed permanently — but it
also should not reappear on every single page view, which is what made closing
it pointless before.

..  note::
    Dragging and closing are progressive enhancements. Without JavaScript the
    hint is still rendered and positioned entirely through CSS; only the
    interaction is unavailable.

..  note::
    The hint is marked :code:`aria-hidden` and excluded from the tab order on
    purpose: it is a development artifact rather than page content, and should
    not be announced to assistive technology on a staging system. The close
    button's touch target is nonetheless enlarged to the 44×44 px minimum.
