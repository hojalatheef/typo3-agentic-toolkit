# Form sets, storage, uploads and RTE

## Form sets: YAML auto-discovery (Feature-109412, 14.2)

Every active extension may ship one or more sets:

```text
EXT:my_extension/Configuration/Form/<AnyName>/config.yaml
```

```yaml
name: my-vendor/my-form-set      # unique, matched by disabledSets
label: 'My Custom Form Set'      # diagnostic output
priority: 200                    # default 100; core typo3/form-base is 10
# everything else is form configuration:
persistenceManager:
  allowedExtensionPaths:
    10: 'EXT:my_extension/Resources/Private/Forms/'
prototypes:
  standard:
    formElementsDefinition: {}
```

- Sets merge in ascending `priority` with `array_replace_recursive()`; loaded for frontend and backend.
- The core base set (`EXT:form/Configuration/Form/Base/config.yaml`) uses YAML `imports:` to split element,
  validator and finisher definitions into files - the same pattern works in your set.
- Disable a set: `$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['form']['disabledSets'][] = 'some-vendor/set';`.
- Deprecated (removal v15): `plugin.tx_form.settings.yamlConfigurations` and
  `module.tx_form.settings.yamlConfigurations` (Deprecation-109412).
- `__inheritances` is deprecated since 14.0 (Deprecation-97857): use YAML anchors and aliases.

Site set `typo3/form` settings (no YAML or TypoScript needed for template overrides):
`form.templates.templateRootPath`, `form.templates.partialRootPath`, `form.templates.layoutRootPath`,
`form.translation.translationFile`.

## Database storage (Feature-108653, 14.2)

Storage adapters (chain of responsibility, `StorageAdapterInterface::supports()`):

| Adapter | Priority | Status |
| --- | --- | --- |
| `DatabaseStorageAdapter` (table `form_definition`: `label`, `identifier`, `configuration` JSON) | 100 | new, recommended |
| `ExtensionStorageAdapter` (`allowedExtensionPaths`) | 75 | unchanged |
| `FileMountStorageAdapter` (`allowedFileMounts`) | 50 | deprecated (Deprecation-108653) |

- Writes go through DataHandler (permissions, history, hooks). The record list links to the Form Editor; delete only
  through the Form Manager; "New record" is denied via `mod.web_list.deniedNewTables`.
- Backend users need `tables_select` / `tables_modify` for `form_definition`.
- **No per-group isolation yet**: all users with table access see all database forms. Keep file mounts if you rely on
  file-mount permission separation.

Migration:

```bash
bin/typo3 form:definition:transfer --source=filemount --target=database --dry-run
bin/typo3 form:definition:transfer --source=filemount --target=database --move
bin/typo3 form:definition:transfer --source=extension --target=database --form-identifier=contact
```

Options: `--source`, `--target` (`database`, `extension`, `filemount`), `--target-location` (PID for database),
`--form-identifier`, `--move`, `--dry-run`. The upgrade wizard "Migrate file-based forms to database storage"
copies forms, updates `tt_content` (CType `form_formframework`) references and deletes the YAML files. Afterwards set
`persistenceManager.allowedFileMounts: ~`.

## Multiple file uploads (Feature-105708, 14.2)

```yaml
- type: FileUpload
  identifier: attachments
  label: 'Attachments'
  properties:
    multiple: true
    allowRemoval: true
    saveToFileMount: '1:/user_upload/'
    allowedMimeTypes:
      - application/pdf
  validators:
    - identifier: Count
      options:
        minimum: 1
        maximum: 5
```

- Works for `FileUpload` and `ImageUpload`; checkbox in the form editor inspector.
- Removal uses HMAC-signed requests; custom templates use
  `<formvh:form.uploadDeleteCheckbox property="{element.identifier}" fileReference="{file}" fileIndex="{iterator.index}" />`.
- Value is `ObjectStorage<FileReference>` when `multiple` is on. Core finishers (Email, SaveToDatabase,
  DeleteUploads) handle both; custom finishers must too.
- Validators run on the whole collection unless they implement
  `TYPO3\CMS\Form\Mvc\Validation\ObjectStorageElementValidatorInterface` (then per element, e.g. MIME type, size).

## Rich text in the form editor (Feature-108966, 14.2)

Textarea editors accept `enableRichtext: true` and `richtextConfiguration: <preset>` (default `form-label`).
New presets: `form-label` (bold, italic, link), `form-content` (+ lists). Enabled out of the box for `StaticText`,
`Checkbox` labels and the `Confirmation` finisher.

```yaml
prototypes:
  standard:
    formElementsDefinition:
      StaticText:
        formEditor:
          editors:
            300:
              identifier: staticText
              templateName: Inspector-TextareaEditor
              label: formEditor.elements.StaticText.editor.staticText.label
              propertyPath: properties.text
              enableRichtext: true
              richtextConfiguration: form-label
```

Sanitising: backend via the preset's `processing.HTMLparser_db.htmlSanitize.build`, frontend via
`f:sanitize.html()` (default builder). Custom partials must keep
`-> f:sanitize.html() -> f:transform.html()`.
