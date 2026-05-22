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
- Code registers `beforeFormCreate`, `beforeFormDuplicate`, `beforeFormDelete`, `beforeFormSave`,
  `initializeFormElement`, `beforeRemoveFromParentRenderable`, `afterSubmit`, `beforeRendering` or
  `afterBuildingFinished` hooks: they were removed in 14.0, use the events.
- Forms stored in file mounts (`persistenceManager.allowedFileMounts`, deprecated): plan migration to the
  `form_definition` table.
- Upload fields should accept several files, or custom finishers handle uploads.

## Steps

1. Form set: copy [templates/config.yaml](templates/config.yaml) to
   `Configuration/Form/<SetName>/config.yaml`. Set a unique `name` (`vendor/name`) and `priority` > 10 (core base set
   `typo3/form-base` is 10; default 100). No TypoScript registration.
2. Custom element: define it under `prototypes.standard.formElementsDefinition.<Type>` in that file (see the
   template), add a frontend partial `<Type>.html` in your partial root path. Omit `formEditorPartials` so the
   editor uses the generic `<typo3-form-form-element-stage-item>` web component.
3. Finisher: copy [templates/NotifyCrmFinisher.php](templates/NotifyCrmFinisher.php); register it under
   `finishersDefinition` and add it to the `Form` editor's finisher select (template shows both). Handle both
   `FileReference` and `ObjectStorage<FileReference>` values.
4. Events: copy [templates/FormEventListener.php](templates/FormEventListener.php) and keep only the listeners
   you need. Event table: [references/events.md](references/events.md).
5. Storage: editors pick the storage (database, extension, file mount) in the Form Manager wizard; migrate
   file-mount forms with the upgrade wizard or `bin/typo3 form:definition:transfer --source=filemount --target=database --dry-run`
   first. Read the permission caveat in [references/storage-and-config.md](references/storage-and-config.md).
6. Frontend template overrides: use the `typo3/form` site set settings `form.templates.*Path` and
   `form.translation.translationFile` instead of `yamlSettingsOverrides`.

## Checklist

- [ ] No `yamlConfigurations` TypoScript; one `config.yaml` per form set with a unique `name`.
- [ ] No `__inheritances` in form YAML (deprecated 14.0, Deprecation-97857); use YAML anchors/aliases.
- [ ] No `SC_OPTIONS['ext/form']` hooks; listeners use `#[AsEventListener]`.
- [ ] Custom element YAML has `implementationClassName`, `formEditor.label`, `group`, `groupSorting`,
      `iconIdentifier`; no `formEditorPartials` unless a special stage rendering is required.
- [ ] Finishers read options with `parseOption()` and support single and multiple uploads.
- [ ] Per-file validators implement `ObjectStorageElementValidatorInterface`; `Count` limits the number of files.
- [ ] Password fields saved with `SaveToDatabase` have `hashed: true` (Feature-106752).
- [ ] RTE-enabled properties are rendered through `f:sanitize.html()` in custom partials.
- [ ] Before migrating to database storage: no file-mount based permission separation in use.

## Pitfalls

- Database-stored forms have no per-group isolation yet: everyone with table access to `form_definition` sees all
  forms. Keep file storage if editor groups must be separated.
- The upgrade wizard updates `persistenceIdentifier` only in `tt_content` (CType `form_formframework`); custom
  tables/FlexForms need manual migration.
- With `multiple: true`, `$formRuntime[$id]` is an `ObjectStorage`, not a `FileReference`.
- Disabling `typo3/form-base` via `disabledSets` breaks rendering entirely.
- `BeforeFormIsDeletedEvent` and `BeforeRenderableIsRemovedFromFormEvent` are stoppable: setting the prevent flag
  skips later listeners.
