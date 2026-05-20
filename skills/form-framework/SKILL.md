---
name: form-framework
description: >-
  Extend the TYPO3 v14 form framework (EXT:form) - form sets auto-discovered from Configuration/Form/<Set>/config.yaml, database storage of form definitions (form_definition, form:definition:transfer), custom form elements without JavaScript, custom finishers, the PSR-14 form events that replaced all ext/form hooks, multiple file uploads, RTE in the form editor and password hashing in SaveToDatabase. Use when adding a form element, finisher, validator or form event listener, when migrating yamlConfigurations TypoScript, ext/form hooks or file-mount forms, or when the user asks to "add a custom form field", "write a form finisher" or "store forms in the database".
argument-hint: "[element|finisher|event|migrate] [extension path]"
allowed-tools: Read, Grep, Glob, Edit, Write, Bash(*/bin/typo3 form:definition:transfer*--dry-run*)
paths:
  - "**/Configuration/Form/**/*.yaml"
  - "**/*.form.yaml"
  - "**/Classes/Domain/Finishers/**/*.php"
  - "**/Classes/**/Form/**/*.php"
---

# Form framework (TYPO3 v14)

Arguments: `$ARGUMENTS`.

In 14.0 every `$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/form'][...]` hook was replaced by a PSR-14 event.
14.2 adds auto-discovered form sets (Feature-109412), database storage (Feature-108653), JavaScript-free custom
elements (Feature-107058), multiple uploads (Feature-105708) and RTE editing (Feature-108966).

## When to use

- An extension ships form configuration: create a form set `Configuration/Form/<Set>/config.yaml`, drop
  `plugin.tx_form.settings.yamlConfigurations` / `module.tx_form...` TypoScript (Deprecation-109412).
- A new form element type: YAML definition + Fluid partial, no `formEditorPartials`, no stage JavaScript.
- Post-submit processing: a finisher class extending `AbstractFinisher`.
