# Content Blocks or plain TCA?

Content Blocks (`friendsoftypo3/content-blocks`) is an extension, not core.
This guide compares it with the core-only path: TCA in
`Configuration/TCA/Overrides/tt_content.php`, Record objects for rendering
and a site set for TypoScript.

## Comparison

| Aspect | Content Blocks | TCA + site set |
| --- | --- | --- |
| Source of truth | one `config.yaml` per element | TCA PHP arrays, TypoScript, templates |
| Dependency | community extension, versioned separately | core only |
| Database columns | generated from YAML | generated from TCA (core auto-creates most columns) |
| Labels | conventional language file in the element folder | your own XLIFF files |
| Templates | conventional files in the element folder | any path you configure |
| Wizard entry, icon | from YAML and the element folder | `addRecordType()` |
| Advanced TCA (custom renderType, itemsProcessors, complex displayCond) | limited; check docs | everything |
| Learning curve for integrators | low | medium |
| Portability between projects | copy one folder | copy several files |

## Decision rules

1. Building a distributable extension for TER/Packagist: use TCA. Do not force
   a Content Blocks dependency on your users.
2. Building one project's site package with 10+ simple elements: Content
   Blocks saves time and keeps each element in one place.
3. Needing custom FormEngine behaviour or tight control of columns: TCA.
4. A team without PHP experience maintains elements: Content Blocks.
5. Mixed is fine: a site package can use Content Blocks for most elements and
   plain TCA for the few that need more.

## Migration paths

- From plain TCA to Content Blocks: reuse existing columns with
  `useExistingField` where possible; otherwise migrate data with an upgrade
  wizard. Plan this per element; do not convert everything at once.
- From Content Blocks to plain TCA: inspect the generated TCA in the backend
  "Configuration" module, copy it into `Configuration/TCA/Overrides/`, keep
  the column names, then remove the element folder.
- Check the extension documentation for your version for its own migration
  notes between major versions of Content Blocks - folder and file names have
  changed between its releases.
