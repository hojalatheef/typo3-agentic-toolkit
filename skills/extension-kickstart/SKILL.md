---
name: extension-kickstart
description: Create a new TYPO3 v14 extension from scratch - composer.json as the single metadata source (title, version, providesPackages; no ext_emconf.php), Services.yaml, a site set with typed settings, a translation domain, the extension icon, README and git files - then hand each requested feature (plugin, tables, content element, backend module, command, Fluid components, tests, docs) to the build skill that owns it and prove the skeleton with composer validate and the fast quality gate. Use when the user asks to "create / kickstart / scaffold a new extension", "start an extension from scratch", "set up an empty extension for TYPO3 14" or "add a new package under packages/".
argument-hint: "<vendor/package-name> [extension_key] [target directory, default packages/]"
allowed-tools: Read, Grep, Glob, Write, Edit, Agent, Bash(mkdir -p*), Bash(cp -R*), Bash(ls:*), Bash(git init*), Bash(git status*), Bash(composer validate*), Bash(composer config*), Bash(composer require*), Bash(ddev composer*), Bash(t3-changelog:*), Bash(find:*), Bash(php -l*)
---

# Extension kickstart for TYPO3 v14

A new v14 extension needs fewer files than older guides suggest. `composer.json` carries all metadata, including title, version and extension dependencies (Feature-108345, 14.2). A site set replaces static TypoScript includes (Feature-103437, 13.1). A translation domain replaces long `LLL:EXT:` paths (Feature-107436, 14.0). Everything else is added only when a feature needs it.

Arguments: `$ARGUMENTS`: Composer name, optional extension key, optional target directory.

## When to use

- Starting a new extension, either inside a TYPO3 project (`packages/<name>/`) or as its own repository.
- The user asks for an empty but correct skeleton to build on.

Not for site packages or themes (use `sitepackage`). Not for an existing extension: use the matching build skill or `feature-team`. To move an old extension to v14, use the `typo3-extension-migration` plugin.

## Step 1: Confirm the inputs

Derive defaults from the arguments, then show them **in one block** and wait for a yes or corrections. Do not write any file before that.

| Input | Default | Rule |
| --- | --- | --- |
| Composer name | from argument | `vendor/package-name`, lower case, dashes |
| Extension key | package name with `-` → `_` | lower case, `a-z0-9_`, no leading `tx_`, `user_` or `pages`, 3–30 characters |
| PHP namespace | `Vendor\PackageName` | StudlyCase of both parts |
| Title and description | ask | written as `"Title - description"` (Breaking-108304) |
| TYPO3 range | `^14.3` | `^13.4 \|\| ^14.3` only if the user needs v13; then see [references/composer-metadata.md](references/composer-metadata.md) |
| PHP range | `^8.2` | the lowest PHP the TYPO3 range supports |
| Target directory | `packages/<package-name>` in a project, `.` in an empty repository | must not exist or must be empty |
| Features | ask | list from [references/feature-map.md](references/feature-map.md) |
| Distribution | Packagist/Composer only | add TER/classic-mode notes only on request |

Detect the context first:

- A project `composer.json` that requires `typo3/cms-core`, or a `config/system/` folder, means this is a TYPO3 project. Use `packages/` and wire it in during Step 5.
- An empty git repository, or none, means a standalone extension repository with the extension at the root.
- A `.ddev/` folder means Composer commands run through `ddev composer`.

## Step 2: Copy and rename the skeleton

Copy [templates/my_extension/](templates/my_extension/) to the target. Then replace every placeholder in all files and folder names:

| Placeholder | Replace with |
| --- | --- |
| `my-vendor/my-extension` | Composer name |
| `my_extension` | extension key |
| `MyVendor\\MyExtension\\` (JSON), `MyVendor\MyExtension\` (YAML/PHP) | PHP namespace |
| `MyExtension` (set folder, TypoScript, settings category) | StudlyCase package name |
| `myExtension.` (settings keys) | lowerCamelCase package name |
| `My Extension - ...` | title and description |

Verify that no placeholder is left: `grep -rn -e my_extension -e MyExtension -e my-vendor <target>` must print nothing.

What the skeleton contains, and why:

| File | Purpose |
| --- | --- |
| `composer.json` | The only metadata file: type, extension key, `version`, `Package.providesPackages` (Feature-108345). No `ext_emconf.php`. |
| `Configuration/Services.yaml` | Autowiring and autoconfiguration for `Classes/`, with Extbase models excluded |
| `Configuration/Sets/<Name>/config.yaml` | Site set, so integrators add the extension through a site's `dependencies` |
| `Configuration/Sets/<Name>/settings.definitions.yaml` | Typed settings that editors change in the site settings module, instead of TypoScript constants |
| `Configuration/Sets/<Name>/setup.typoscript` | TypoScript loaded for every site that uses the set |
| `Resources/Private/Language/messages.xlf` | Default translation domain `<key>.messages` |
| `Resources/Public/Icons/Extension.svg` | Extension icon, picked up automatically |
| `Classes/.gitkeep` | Lets the `Services.yaml` resource resolve before the first class exists. Delete it once a class is added. |
| `README.md`, `.gitattributes`, `.gitignore` | Repository basics. `.gitattributes` keeps tests and build files out of dist archives. |

Do not add `ext_localconf.php`, `ext_tables.php`, `ext_tables.sql`, `Configuration/TCA/` or `Tests/` yet. The features in Step 3 create them when they need them.

## Step 3: Hand each feature to its skill

For each requested feature, follow the build skill named in [references/feature-map.md](references/feature-map.md). That skill owns the files and the changelog facts for the feature. Short version:

| Feature | Skill |
| --- | --- |
| Extbase plugin | `extbase-modern`, registered as its own `CType` (Important-105538) |
| Database tables / records | `schema-api` |
| Content element | `record-rendering` (core) or `content-blocks` (extension) |
| Backend module | `backend-modules` |
| Console command, scheduler task, queue handler | `scheduler-console` |
| Fluid components | `fluid-components` |
| Labels beyond the default domain | `translations` |
| Tests | `functional-tests` |
| Documentation | `docs-rst` |

If more than three features were requested, or they depend on each other (a plugin that lists records from a new table, plus a backend module), stop building yourself. Commit the skeleton and hand the feature list to the `feature-team` skill. It plans disjoint packages and builds them in parallel.

## Step 4: QA tooling (default yes)

Add the development dependencies that `quality-gate` expects:

```bash
composer require --dev typo3/coding-standards phpstan/phpstan saschaegerer/phpstan-typo3 ssch/typo3-rector typo3/testing-framework
vendor/bin/typo3-coding-standards setup extension
```

Use the major versions that match the TYPO3 range. For PHPUnit configs and the first test, follow the `functional-tests` skill. Inside a project, these packages belong in the **project's** `require-dev`, not in the extension's `composer.json`. A standalone repository keeps them in its own `require-dev`.

## Step 5: Wire it into a project

Skip this step for a standalone repository.

1. The project `composer.json` needs the path repository: `composer config repositories.local path 'packages/*'`. Skip if it already exists.
2. `composer require my-vendor/my-extension:@dev`, prefixed with `ddev` when there is a `.ddev/` folder.
3. `vendor/bin/typo3 extension:setup` creates tables and runs the set-up of the new extension.
4. Add the set to the site: `dependencies: [my-vendor/my-extension]` in `config/sites/<site>/config.yaml` (Feature-103437), or use the Sites module.
5. `vendor/bin/typo3 cache:flush`.

Ask before steps 2 to 5. They change the project's lock file, database and site configuration.

## Step 6: Prove it

1. `composer validate --strict` in the extension folder.
2. `php -l` on every PHP file that was created.
3. Inside a project: `vendor/bin/typo3 site:sets:list` shows the new set.
4. Run the `quality-gate` skill with `--fast`. Report SKIPPED steps as gaps, not as passes.

## Checklist

- [ ] `composer.json`: `type: typo3-cms-extension`, `extra.typo3/cms.extension-key`, `extra.typo3/cms.version`, `extra.typo3/cms.Package.providesPackages` (at least `{}`), description in the form `"Title - description"`, `license`, PSR-4 autoload.
- [ ] Every TYPO3 extension it depends on is in `require` (classic mode resolves load order from there, Feature-108345).
- [ ] No `ext_emconf.php` unless v13 classic mode or TER is a stated requirement (Deprecation-108345).
- [ ] Exactly one public site set, named like the Composer package.
- [ ] Editor-facing options are site settings with `type` and `default`, not TypoScript constants.
- [ ] Labels live in `Resources/Private/Language/messages.xlf` (domain `<key>.messages`), with no `locallang.xlf` next to it.
- [ ] Templates use `*.fluid.html` for a v14-only extension (Feature-108166).
- [ ] No placeholder strings are left.
- [ ] `composer validate --strict` exits 0.

## Report

```text
EXTENSION  my-vendor/my-extension (my_extension) at packages/my-extension
TYPO3      ^14.3   PHP ^8.2   metadata: composer.json only
FILES      <n> created (<short list>)
FEATURES   plugin -> extbase-modern done | tables -> feature-team (plan .typo3-toolkit/plan.md)
WIRED      composer require ok | extension:setup ok | site dependency added | skipped (standalone)
GATE       composer validate PASS | quality-gate --fast <verdict>
NEXT       <the next useful step>
```

## References

- [references/composer-metadata.md](references/composer-metadata.md): every `composer.json` field, version and stability, `providesPackages`, and supporting v13 next to v14.
- [references/feature-map.md](references/feature-map.md): feature → skill → files → changelog.
- [templates/my_extension/](templates/my_extension/): copy-ready skeleton.
- Related skills: `sitepackage`, `site-sets`, `translations`, `feature-team`, `quality-gate`, `docs-rst`, `release`.
- Changelog: Feature-103437 (13.1), Breaking-108304, Feature-107436, Feature-108166, Important-105538 (14.0), Feature-108345, Deprecation-108345 (14.2).
