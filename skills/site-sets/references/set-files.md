# Site set files in detail

Every key on this page is taken from the TYPO3 changelog entries cited next to
it. Anything not listed there is out of scope here - check the TYPO3 Site
Handling documentation for your exact version before using it.

## config.yaml

Source: Feature-103437 (13.1).

```yaml
name: my-vendor/my-set
label: My Set

# Load TypoScript, TSconfig and settings from dependencies
dependencies:
  - some-namespace/slider
  - other-namespace/fancy-carousel
```

- `name` - `vendor/name`; this is what sites list. Folder name does not matter.
- `label` - shown in the backend set selection.
- `dependencies` - sets loaded first, recursively, ordered and deduplicated.
