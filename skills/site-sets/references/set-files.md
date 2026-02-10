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
- `hidden: true` - hides a helper set from the backend selection and
  `site:sets:list`. A hidden set that is already applied stays visible in the
  site's list of active sets.

Integrators can hide sets for editors in user TSconfig:

```typoscript
options.sites.hideSets := addToList(typo3/fluid-styled-content)
```

A site applies sets in `config/sites/<site>/config.yaml`:

```yaml
base: 'http://example.com/'
rootPageId: 1
dependencies:
  - my-vendor/my-set
```

`optionalDependencies` has no changelog entry, but the v13 core
`SetDefinition`/`SetRegistry` read it: listed sets are ordered before yours
only if they are installed, and a missing one is not an error. Use it for
