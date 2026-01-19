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
