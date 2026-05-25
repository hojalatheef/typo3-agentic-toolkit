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
