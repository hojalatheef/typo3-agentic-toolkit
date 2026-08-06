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

## Camino or own site package

TYPO3 v14.1 ships the default theme **Camino** (`typo3/theme-camino`,
Feature-108539): site set "Theme: Camino", four color schemes in site
settings, start/content page backend layouts, hero and content elements,
no dependency on `fluid_styled_content`. On classic-mode fresh installs it is
enabled by default; in Composer mode require `typo3/theme-camino`.

- Use Camino for demos, prototypes and small sites that fit its options.
- Build your own site package for custom design and long-lived projects:
  Camino "is not meant to evolve within TYPO3" and moves to a separate
  repository in v15.0. Read it as a reference implementation of site sets
  and PAGEVIEW.

## Steps

1. Copy the skeleton from [templates/my_sitepackage/](templates/my_sitepackage/)
   to `packages/my_sitepackage/`, rename vendor, package and extension key.
2. `composer require my-vendor/my-sitepackage:@dev` (path repository
   `packages/*`).
3. Add the set to the site: in `config/sites/<site>/config.yaml` set
   `dependencies: [my-vendor/my-sitepackage]` (Feature-103437). Remove the
   `sys_template` record if there is one (Feature-103439).
4. Pick the backend layout "Default" on the root page. `PAGEVIEW`
   renders `Resources/Private/PageView/Pages/Default.fluid.html`
   (Feature-103504).
5. Render content with `<f:render.contentArea contentArea="{content.main}" />`
   (Feature-104974, Feature-108726, 14.2).
6. Head and body data from Fluid: `<f:page.title>` (Feature-107358),
   `<f:page.meta>` (Feature-107359), `<f:page.headerData>` /
   `<f:page.footerData>` (Feature-107056).
