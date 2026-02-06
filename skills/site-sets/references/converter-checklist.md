# Converter checklist: static templates to a site set

Use this when an extension or site package still ships TypoScript through
`ext_typoscript_setup.typoscript`, `ext_typoscript_constants.typoscript`,
`Configuration/TCA/Overrides/sys_template.php` (`addStaticFile()`) or a
`sys_template` record. Rules come from Feature-103437 and Feature-103439 (13.1).

## Inventory

Run these from the extension root and note every hit:

```bash
grep -rn "addStaticFile" Configuration/ ext_*.php
ls ext_typoscript_setup.typoscript ext_typoscript_constants.typoscript 2>/dev/null
grep -rn "@import\|INCLUDE_TYPOSCRIPT" Configuration/ ext_*.typoscript
grep -rn "addPageTSConfig\|addUserTSConfig" ext_localconf.php ext_tables.php
ls Configuration/page.tsconfig Configuration/user.tsconfig 2>/dev/null
```

Classify each piece:

| Piece | Goes to |
| --- | --- |
| Frontend TypoScript (`plugin.tx_*`, `lib.*`, `page`) | set `setup.typoscript` |
| Constants that integrators change | set `settings.definitions.yaml` |
| Constants that are internal | set `constants.typoscript` |
| Site-specific page TSconfig (backend layouts, TCEFORM for this site) | set `page.tsconfig` |
| Backend-module TypoScript (`module.tx_*`) | stays global |
| User TSconfig | stays in `Configuration/user.tsconfig` |
| Route enhancers the extension needs | set `route-enhancers.yaml` (v14.1+) |

## Conversion steps

1. Create one set per former static template. Keep the old static template
   while v12 (or `sys_template`-based) sites must still work.
2. Copy setup TypoScript into `setup.typoscript`. Replace cross-extension
   `@import` with a `dependencies` entry for the other extension's set.
3. For every constant an editor or integrator changes, add a definition:

   ```yaml
   settings:
     plugin.tx_myext.itemsPerPage:
       label: 'Items per page'
       category: myext
       type: int
       default: 10
   ```

   Keep the constant identifier as the setting identifier so `{$...}`
   references in `setup.typoscript` keep working: site settings overrule set
   constants of the same name.
4. When renaming a constant, bridge the old name with
   `{$old.name ?? $new.name}` (Feature-103671).
5. Move site-specific page TSconfig into `page.tsconfig` (Feature-103522) and
   drop the global registration if it was only there for this site.
6. Decide on `ext_typoscript_setup.typoscript`: it is not loaded for
   set-based sites. Keep it only for legacy `sys_template` setups; never rely
   on it for new ones.
7. Register `hidden: true` helper sets for pieces that should only be pulled
   in as dependencies.
