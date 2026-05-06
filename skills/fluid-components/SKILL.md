---
name: fluid-components
description: Build Fluid components and a component-based design system in TYPO3 v14 - ComponentCollections.php, Namespaces.php, f:argument and f:slot, *.fluid.html files, union types, CDATA syntax, XSD autocomplete, fluid:analyze, fluid:namespaces and cache warmup. Use when the user asks to "create a Fluid component", "set up a component library / design system", "register a global ViewHelper namespace", "get autocomplete for components", "lint Fluid templates" or edits files under Resources/Private/Components or Configuration/Fluid/.
argument-hint: "[component path, e.g. molecule.teaserCard] [extension path]"
allowed-tools: Read, Grep, Glob, Edit, Write, Bash(vendor/bin/typo3 fluid:*), Bash(ddev exec vendor/bin/typo3 fluid:*), Bash(vendor/bin/typo3 cache:warmup*)
paths:
  - "**/Resources/Private/Components/**"
  - "**/Configuration/Fluid/ComponentCollections.php"
  - "**/Configuration/Fluid/Namespaces.php"
  - "**/*.fluid.html"
---

# Fluid components

Fluid components are template files with a declared API (`<f:argument>`,
`<f:slot>`) that are called like ViewHelpers: `<my:molecule.teaserCard />`.
TYPO3 v14.1 registers component collections with a configuration file instead
of a PHP class (Feature-108508).

Arguments: `$ARGUMENTS` - optional component path in dot notation and an
optional extension path.

## When to use

- A site package or extension needs reusable UI pieces (buttons, cards,
  teasers, navigation) with a typed API.
- Partials are passed `{_all}` and nobody knows which variables they need.
- A team wants IDE autocomplete and CI checks for Fluid templates.
- A global ViewHelper namespace is still registered in
  `$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']` (deprecated in
  14.1, Deprecation-108524).

Components via configuration need TYPO3 v14.1+. On v13 a class-based
collection still works; both can ship side by side (see
[references/components-in-depth.md](references/components-in-depth.md)).

## Steps

1. Register the collection in `Configuration/Fluid/ComponentCollections.php`
   ([templates/ComponentCollections.php](templates/ComponentCollections.php)):

   ```php
   return [
       'MyVendor\\MyExtension\\Components' => [
           'templatePaths' => [
               10 => 'EXT:my_extension/Resources/Private/Components',
           ],
       ],
   ];
   ```

2. Optionally make the prefix global in `Configuration/Fluid/Namespaces.php`
   (Feature-108524) so templates do not need an `xmlns:` line:

   ```php
   return [
       'my' => ['MyVendor\\MyExtension\\Components'],
   ];
   ```

3. Create one folder per component. Default layout:
   `<my:molecule.teaserCard />` resolves to
   `Resources/Private/Components/Molecule/TeaserCard/TeaserCard.fluid.html`.
4. Declare every argument with `<f:argument>` (strict API is the default) and
   receive HTML via `<f:slot />`. Start from
   [templates/TeaserCard.fluid.html](templates/TeaserCard.fluid.html).
5. Generate XSD files for autocomplete: `vendor/bin/typo3 fluid:schema:generate`
   (Feature-104114, 13.2; covers components since Feature-109114, 14.2).
6. Check templates: `vendor/bin/typo3 fluid:analyze` (Feature-108763, 14.2) and
   `vendor/bin/typo3 fluid:namespaces` (Feature-108846, 14.2).
7. Warm caches on deploy: `vendor/bin/typo3 cache:warmup` compiles all
   `*.fluid.*` files (Feature-108229, 14.0).

## Component rules

| Rule | Why |
| --- | --- |
| Use `*.fluid.html` for new files in v14-only code | preferred in resolving; required by `fluid:analyze` and warmup (Feature-108166) |
| Keep `.html` in extensions that still support v13 | older Fluid ignores `*.fluid.*` (Feature-108166) |
| Declare all arguments; set `optional="{true}"` where needed | undeclared arguments fail unless `additionalArgumentsAllowed` |
| Union types (pipe-separated class or type names in `type`) for flexible args | Fluid 5 (Feature-108148); disables automatic type conversion |
