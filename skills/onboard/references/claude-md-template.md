# CLAUDE.md template

Fill every placeholder from the repository. Remove sections that do not apply. Keep the result short: Claude reads it at the start of every session.

## Template

````markdown
# <Project or extension name>

<One sentence: what it is and who uses it.>

## Stack

- TYPO3 <installed version> (constraint `<constraint>`), PHP <version>
- <Composer project with DDEV | Extension `<vendor/package>`, extension key `<key>`>
- Database: <MariaDB 10.11 | MySQL 8 | PostgreSQL | SQLite in tests>

## Commands

| Task | Command |
| --- | --- |
| Install | `ddev composer install` |
| Clear caches | `ddev typo3 cache:flush` |
| Lint YAML | `ddev typo3 lint:yaml config/ packages/` |
| Analyse Fluid | `ddev typo3 fluid:analyze` |
| PHPStan | `ddev exec vendor/bin/phpstan analyse` |
| Coding standards | `ddev exec vendor/bin/php-cs-fixer fix --dry-run --diff` |
| Unit tests | `Build/Scripts/runTests.sh -s unit` |
| Functional tests | `Build/Scripts/runTests.sh -s functional -d sqlite` |

## Layout

- `packages/<sitepackage>/` - site package (site set `<vendor/set>`)
- `config/sites/<site>/config.yaml` - site configuration
- <other important folders>

## Conventions

- Target TYPO3 v14 APIs. Configuration goes into site sets, not static TypoScript includes.
- <Fluid components live in ...>
- <Templates use the `*.fluid.html` extension>
- Tests: CSV fixtures in `Tests/Functional/Fixtures/`.
- Commit messages: `[TASK]`, `[FEATURE]`, `[BUGFIX]`, `[DOCS]` prefixes.

## Do not

- Edit `vendor/`, `public/typo3/`, `var/` or generated files.
- Run `composer update` without being asked.
````
