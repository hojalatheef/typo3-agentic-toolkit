---
name: site-sets
description: Build TYPO3 v13/v14 site sets for an extension or site package - config.yaml, settings.definitions.yaml, settings.yaml, setup.typoscript, page.tsconfig, route-enhancers.yaml, dependencies, categories, enum labels and the site settings editor. Use when the user asks to "add a site set", "ship TypoScript without sys_template", "replace static templates / ext_typoscript_setup", "add site settings", "make constants editable in the backend" or edits files under Configuration/Sets/.
argument-hint: "[vendor/set-name] [extension path]"
allowed-tools: Read, Grep, Glob, Edit, Write, Bash(vendor/bin/typo3 site:sets:list*), Bash(ddev exec vendor/bin/typo3 site:sets:list*)
paths:
  - "**/Configuration/Sets/**"
  - "**/config/sites/**/config.yaml"
  - "**/config/sites/**/settings.yaml"
  - "**/ext_typoscript_setup.typoscript"
  - "**/ext_typoscript_constants.typoscript"
  - "**/Configuration/TypoScript/**"
---

# Site sets

Site sets ship settings, TypoScript, page TSconfig and route enhancers as one
composable unit that a site lists under `dependencies`. They replace
`sys_template` records, `static_file_include` and global
`ext_typoscript_*.typoscript` files for new work.

Arguments: `$ARGUMENTS` - an optional set name (`vendor/name`) and an optional
extension path (default: current directory).

Target: TYPO3 v14 LTS. Everything below exists since v13.1 unless a newer
changelog ID is given.

## When to use

- An extension or site package needs to ship TypoScript, TSconfig or settings.
- TypoScript constants should become editable settings in the backend.
- An extension still relies on `ext_typoscript_setup.typoscript` or a static
  template registered with `ExtensionManagementUtility::addStaticFile()`.
- A site needs route enhancers that belong to an extension (v14.1+).

Do not use it for global, backend-only TypoScript (for example
`module.tx_form` YAML registration) - that stays global (Feature-103439).

## Files of a set

One folder per set below `Configuration/Sets/`. The folder name is arbitrary,
the `name` in `config.yaml` is what sites reference (Feature-103437, 13.1).

| File | Purpose | Since |
| --- | --- | --- |
| `config.yaml` | `name`, `label`, `dependencies`, optional `optionalDependencies`, `hidden` | Feature-103437, 13.1 (`optionalDependencies`: core source, no changelog) |
| `settings.definitions.yaml` | typed settings with `label`, `description`, `category`, `default`, `readonly`, `enum` | Feature-103437, 13.1 / Feature-104794, 13.3 |
| `settings.yaml` | values for settings of dependencies (not own defaults) | Feature-103437, 13.1 |
| `setup.typoscript`, `constants.typoscript` | TypoScript inserted like a static include | Feature-103439, 13.1 |
| `page.tsconfig` | page TSconfig scoped to the site | Feature-103522, 13.1 |
| `route-enhancers.yaml` | route enhancers merged as presets into the site | Feature-107837, 14.1 |
| `labels.xlf` | labels for the set and its settings, domain `<ext>.sets.<set>` | Feature-93334, 14.0 / Feature-106640, 14.2 |

Copy-ready files are in [templates/](templates/). Details, YAML keys and the
merge rules are in [references/set-files.md](references/set-files.md).

## Steps

1. Pick the set name. Use the extension's Composer vendor. If the extension
   ships exactly one set, use the Composer package name.
2. Create `Configuration/Sets/<Name>/config.yaml` from
   [templates/config.yaml](templates/config.yaml). List every set whose
   TypoScript or settings yours builds on under `dependencies` (for example
   `typo3/fluid-styled-content`). Sets are ordered and deduplicated, so do not
   `@import` another extension's TypoScript.
3. Move settings: every TypoScript constant that an integrator should change
   becomes an entry in `settings.definitions.yaml`. Site settings always win
   over set constants (Feature-103439), so the same identifier used as a
   constant keeps TypoScript `{$...}` references working.
4. Group settings with `categories` and give each one a `category`
   (Feature-104794). Use `enum` for fixed choices; localize options through
   `labels.xlf` (Feature-106640).
5. Move TypoScript into `setup.typoscript` / `constants.typoscript` next to
   `config.yaml`, and page TSconfig into `page.tsconfig`.
6. Move route enhancers that the extension needs into `route-enhancers.yaml`
   (v14.1+ only; keep them in the site config for v13 support).
7. Verify: `vendor/bin/typo3 site:sets:list` shows the set; add it to a site
   via `config/sites/<site>/config.yaml` `dependencies` or the backend site
   configuration; open the settings editor and check every label.

## Converter checklist (static template / ext_typoscript_setup to set)

Use [references/converter-checklist.md](references/converter-checklist.md) for
the full walk-through. Short form:

- [ ] Every `addStaticFile()` registration has a matching set.
- [ ] `ext_typoscript_setup.typoscript` content moved into a set's
      `setup.typoscript` (it is not autoloaded for set-based sites).
- [ ] Constants used by editors exist as settings with label, type and default.
- [ ] Cross-extension `@import` / `<INCLUDE_TYPOSCRIPT:` replaced by `dependencies`.
- [ ] Global `Configuration/page.tsconfig` that is frontend/site specific moved to the set's `page.tsconfig`.
