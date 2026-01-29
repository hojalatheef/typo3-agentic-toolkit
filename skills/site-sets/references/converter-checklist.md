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
