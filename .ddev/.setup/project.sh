# .ddev/.setup/project.sh — repo-owned customizations for `ddev install`.
#
# Not managed by the add-on, survives `ddev add-on get` upgrades.
# See .ddev/.setup/project.sh.example for the full list of available hooks.

# helhum/typo3-console (required by every install) pulls in
# helhum/dotenv-connector, which ships a Composer plugin. Composer blocks
# unknown plugins by default, so allow it explicitly, same as the root
# composer.json already does.
COMPOSER_CONFIG=(
    'allow-plugins.helhum/dotenv-connector true'
)
