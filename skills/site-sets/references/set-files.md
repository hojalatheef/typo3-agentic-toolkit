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
integrations with extensions that may be absent (for example an optional
SEO or news set). Check the Site Handling documentation for your version
before relying on details.

```yaml
name: my-vendor/my-set
label: My Set
dependencies:
  - typo3/fluid-styled-content
optionalDependencies:
  - typo3/seo-sitemap
```

## settings.definitions.yaml and settings.yaml

Sources: Feature-103437 (13.1), Feature-104794 (13.3), Important-106894
(13.4.x), Feature-106640 (14.2).

Types named in the changelog: `int`, `bool`, `string`, `stringlist`, `text`,
`color`. `description` may use Markdown.

```yaml
categories:
  myCategory:
    label: 'My Category'

settings:
  my.example.setting:
    label: 'My example setting'
    description: 'Configure `baz` to be used in `bar`.'
    category: myCategory
    type: string
    default: ''

  my.seoRelevantSetting:
    label: 'My SEO relevant setting'
    # show in EXT:seo provided category "seo"
    category: seo
    type: int
    default: 5

  my.readonlySetting:
    label: 'My readonly setting'
    type: int
    default: 5
    readonly: true
```

- `category` may point at a category of another set (for example `seo`).
  Uncategorized settings land in a virtual "Other" category at the end.
- `readonly: true` shows the value read-only in the editor; it can only be
  changed in `config/sites/<site>/settings.yaml`.
