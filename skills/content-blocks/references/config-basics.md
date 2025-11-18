# Content Blocks basics

Applies to `friendsoftypo3/content-blocks` 1.x (checked against 1.0.4 on
TYPO3 13.4). This is an extension, not core. Newer or older releases may
differ - **check the extension docs for your version** before copying keys.

## Folder layout

A host extension (usually the site package) holds the blocks:

```text
my_sitepackage/
└── ContentBlocks/
    ├── ContentElements/
    │   └── teaser/
    │       ├── config.yaml
    │       ├── assets/
    │       │   ├── icon.svg
    │       │   └── frontend.css        (optional)
    │       ├── language/
    │       │   └── labels.xlf
    │       └── templates/
    │           ├── frontend.html
    │           └── backend-preview.html  (optional)
    ├── PageTypes/
    └── RecordTypes/
```

Folder names `ContentElements`, `PageTypes`, `RecordTypes`, `assets`,
`language` and `templates` are conventions read by the extension. Content
Blocks 0.x used different names (for example `Source/` and `Assets/`); its
upgrade wizard renames them.

## config.yaml keys

| Key | Meaning |
| --- | --- |
| `name` | `vendor/element-name`, unique |
| `typeName` | optional CType value; derived from `name` if omitted |
| `group` | optional wizard group |
| `prefixFields`, `prefixType` | optional column prefixing; default prefixes columns with vendor and name - check the docs |
| `fields` | list of fields |

Each field has an `identifier` and either `useExistingField: true` (reuse a
core column such as `header` or `bodytext`) or a `type`. Field types available
in 1.x include `Text`, `Textarea`, `Number`, `Email`, `Link`, `Select`,
`Radio`, `Checkbox`, `Color`, `DateTime`, `File`, `Folder`, `Relation`,
`Category`, `Collection`, `FlexForm`, `Json`, `Slug`, `Uuid`, `Password`,
`Pass`, `Language`, plus layout types `Palette`, `Tab` and `Linebreak`.
Most other keys are passed through to the matching TCA type, so `required`,
`default`, `items` and `allowedTypes` behave as in TCA.

## Templates and command

- `templates/frontend.html` receives the element as `{data}`. Reused core
  fields are available by their name (`{data.header}`); for your own fields
  check the docs of your version for whether to use the identifier or the
  prefixed column name.
- In 1.x `{data}` is a `ContentBlockData` object that implements the core
  `RecordInterface`, so record-aware core ViewHelpers can take it (see the
  `record-rendering` skill).
