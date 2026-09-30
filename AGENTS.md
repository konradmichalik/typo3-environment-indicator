# AGENTS.md

## Project overview

TYPO3 extension (`typo3_environment_indicator`) that shows visual environment indicators in the TYPO3 frontend and backend: favicon modifications, toolbar items, hints, logos and widgets that tell development, testing, staging and production apart.

- Package: `konradmichalik/typo3-environment-indicator`, namespace `KonradMichalik\Typo3EnvironmentIndicator` (PSR-4, `Classes/`)
- 3.x: PHP 8.2 to 8.5, TYPO3 13.4 and 14.3. 2.x supports TYPO3 11.5 to 13.4 and PHP 8.1 to 8.4

## Structure

- `Classes/Configuration/` central API `Handler::addIndicator()`, plus `Trigger/` and `Indicator/` (`Backend/`, `Frontend/`, `General/`, `Cli/`, `Mail/`)
- `Classes/Image/` image `Factory/` and `Modifier/` classes (text, triangle, circle, frame, colorize, overlay, replace)
- `Classes/Middleware/` PSR-15 middlewares for backend and frontend favicon and backend logo
- `Classes/Backend/`, `Classes/Widgets/`, `Classes/EventListener/`, `Classes/ExpressionLanguage/`, `Classes/TypoScript/`, `Classes/ViewHelpers/`, `Classes/Utility/`, `Classes/Enum/`
- `Configuration/` `RequestMiddlewares.php`, `Services.yaml`, `Icons.php`, `ExpressionLanguage.php`, `Sets/EnvironmentIndicator/` (Site Set), `TCA/`, `TypoScript/`
- `ext_localconf.php` default presets and configuration
- `Resources/` templates, language files, CSS, JavaScript, fonts, icons
- `Tests/Unit/` and `Tests/Functional/` PHPUnit tests, mirror `Classes/`
- `Tests/CGL/` separate Composer project with code style, static analysis and migration tooling
- `Documentation/` reStructuredText docs, rendered with `docker-compose.yml`
- `.ddev/` DDEV setup and commands to install TYPO3 13 and 14 test instances

Core pattern: triggers (`TriggerInterface::check(): bool`) are evaluated, and when all pass, the indicators (`IndicatorInterface::getConfiguration(): array`) are activated.

```php
Handler::addIndicator(
    triggers: [new Trigger\ApplicationContext('Development*')],
    indicators: [
        new Indicator\Favicon([
            new Image\Modifier\TextModifier(['text' => 'DEV', 'color' => '#bd593a']),
        ]),
        new Indicator\Backend\Toolbar(['color' => '#bd593a']),
    ],
);
```

## Development commands

The project uses DDEV. Prefix commands with `ddev`.

```bash
ddev start
ddev composer install
ddev install all      # or: ddev install 13
ddev context          # change application context
ddev 13 typo3 cache:flush
ddev launch
ddev composer docs    # build and open the documentation
```

Lint, fix, static analysis and migration run through `ddev cgl`, which executes the scripts of `Tests/CGL/composer.json`:

```bash
ddev cgl lint       # composer, editorconfig, php, typoscript
ddev cgl fix        # composer, editorconfig, php
ddev cgl sca        # PHPStan
ddev cgl migration  # Rector
```

Single linters: `ddev cgl lint:composer`, `lint:editorconfig`, `lint:php`, `lint:typoscript`. Matching fixers: `fix:composer`, `fix:editorconfig`, `fix:php`.

## Testing

PHPUnit with unit tests (`phpunit.xml`) and functional tests (`phpunit.functional.xml`, TYPO3 testing framework).

```bash
ddev composer test                   # unit and functional, no coverage
ddev composer test:unit
ddev composer test:functional
ddev composer test:coverage          # both suites, merged with phpcov into .Build/coverage/
ddev exec vendor/bin/phpunit Tests/Unit/Path/To/TestFile.php
ddev exec vendor/bin/phpunit --filter testMethodName
```

CI runs the shared `tests-typo3` workflow on TYPO3 13.4 and 14.3 with PHP 8.2 to 8.5. The CGL workflow runs the linters on every push.

## Code style and static analysis

- PHP CS Fixer with `konradmichalik/php-cs-fixer-preset`, config in `Tests/CGL/.php-cs-fixer.php`. The fixer generates the license header, run `ddev cgl fix:php`
- `declare(strict_types=1);` in every PHP file
- PHPStan level 8 with the TYPO3 preset, config and baseline in `Tests/CGL/`
- Rector config in `Tests/CGL/rector.php`
- EditorConfig is enforced via `.editorconfig`
- `composer-require-checker.json` configures the dependency check

## Git workflow

- Commit format: `<type>: <description>` with type one of `feat`, `fix`, `refactor`, `docs`, `test`, `chore`, `perf`, `ci`
- Single-line messages, no co-author trailers
- One commit per logical change, open a pull request against `main`
