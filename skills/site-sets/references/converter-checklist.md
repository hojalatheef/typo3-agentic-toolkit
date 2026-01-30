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
