---
name: schema-api
description: Write TCA the TYPO3 v14 way and read it through the TCA Schema API - TcaSchemaFactory, capabilities, sub-schemata, type-specific title/label/previewRenderer, TCAdefaults per type, itemsProcessors, type country, searchable, allowedRecordTypes, isViewable, auto-created columns and DB fields, FlexForm plugin registration. Use when the user asks to "add a table / field / record type", "write TCA for v14", "drop ext_tables.sql", "replace $GLOBALS['TCA'] reads", "replace itemsProcFunc or searchFields" or edits Configuration/TCA files.
argument-hint: "[table name] [extension path]"
allowed-tools: Read, Grep, Glob, Edit, Write
paths:
  - "**/Configuration/TCA/**/*.php"
  - "**/ext_tables.sql"
  - "**/Configuration/FlexForms/**/*.xml"
---

# TCA and the Schema API

TCA is still written as PHP arrays in `Configuration/TCA/` and
`Configuration/TCA/Overrides/`. Since TYPO3 v13 the core derives columns,
database fields and form tabs from it, and code reads it through the immutable
Schema API instead of `$GLOBALS['TCA']`. TYPO3 v14 moved more options into
`types` so each record type can have its own title, label and defaults.

Arguments: `$ARGUMENTS` - optional table name and extension path.

## When to use

- A new table, field or record type (CType, doktype, `record_type`) is added.
- `ext_tables.sql` repeats what TCA already says.
- TCA `columns` copy boilerplate for `hidden`, `starttime`, `sys_language_uid`.
- PHP code reads `$GLOBALS['TCA']` or calls deprecated `BackendUtility`
  helpers like `isTableLocalizable()`.
- `itemsProcFunc`, `ctrl.searchFields`, `addPiFlexFormValue()` or
  `PageDoktypeRegistry->add()` are in use.

## Write TCA for v14 - checklist

- [ ] `ctrl` lists capabilities (`enablecolumns`, `languageField`, `editlock`,
      `descriptionColumn`, ...) in the base file; their `columns` are
      auto-created (Feature-104311, 13.3). Do not add capabilities in Overrides.
- [ ] No `ext_tables.sql` entries for plain TCA columns; keep only special
      definitions (Feature-101553, 13.0). Use `dbFieldLength` on `select`.
- [ ] Content types list only their own fields in `showitem`; system tabs are
      added automatically (Feature-104814, 13.3).
- [ ] Type-specific `title` and `previewRenderer` in `types` (Feature-108027, 14.0).
- [ ] Type-specific `label`, `label_alt`, `label_alt_force` in `types` or via
      `addRecordType()` third argument (Feature-108581, 14.2).
- [ ] Per-type defaults via `TCAdefaults.<table>.<field>.types.<type>` in TSconfig (Feature-107281, 14.0).
- [ ] `itemsProcessors` instead of `itemsProcFunc` (Feature-107889, 14.0).
- [ ] Items use `label`/`value` keys, also for `valuePicker` (Feature-106092, 14.0).
- [ ] Countries use `'type' => 'country'` (Feature-99911, 14.0).
- [ ] No `ctrl.searchFields`; exclude fields with `'searchable' => false` (Feature-106972, 14.0).
- [ ] Page types use `allowedRecordTypes` and `isViewable` in TCA, not
      `PageDoktypeRegistry` (Feature-108557, Feature-97898, 14.2).
