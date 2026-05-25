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
```
