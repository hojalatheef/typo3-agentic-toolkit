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
