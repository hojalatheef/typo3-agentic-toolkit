---
name: sitepackage
description: Create or modernize a TYPO3 v14 site package - site set as entry point, PAGEVIEW page templates, content areas, f:page.title / f:page.meta / f:page.headerData / f:page.footerData, f:image.srcset, PKG resource identifiers, and when to start from the Camino default theme instead. Use when the user asks to "create a sitepackage / theme", "set up a new TYPO3 14 site", "replace FLUIDTEMPLATE page setup", "add meta tags or responsive images in the page template" or edits a sitepackage's Configuration/Sets or Resources/Private/PageView.
argument-hint: "[vendor/package-name] [target directory, default packages/]"
allowed-tools: Read, Grep, Glob, Edit, Write, Bash(cp -R*), Bash(mkdir -p*), Bash(composer validate*), Bash(vendor/bin/typo3 site:sets:list*), Bash(vendor/bin/typo3 fluid:analyze*)
paths:
  - "**/Resources/Private/PageView/**"
  - "**/Configuration/Sets/**"
  - "**/packages/*/composer.json"
---

# Site package for TYPO3 v14

A v14 site package is a normal extension whose site set is the single entry
point: the site lists the set under `dependencies`, the set ships TypoScript,
page TSconfig (backend layouts) and settings, and `PAGEVIEW` renders Fluid
page templates by backend-layout name. No `sys_template` record, no
`FLUIDTEMPLATE` path juggling.

Arguments: `$ARGUMENTS` - optional Composer name and target directory.

## When to use

- Starting a new TYPO3 v14 project or a fresh theme.
- An existing site package still uses `sys_template` records,
  `ext_typoscript_setup.typoscript`, `FLUIDTEMPLATE` with
  `templateName` from `backend_layout`, or TypoScript `page.meta`/`headerData`
  for things Fluid can now do.
- Someone asks whether to build a site package at all: for a quick standard
  site, Camino may be enough (see below).
