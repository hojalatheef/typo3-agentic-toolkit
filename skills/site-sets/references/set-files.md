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
- `settings.yaml` next to `config.yaml` sets values for settings of
  dependencies, for example `styles.content.defaultHeaderType: 1` for
  `typo3/fluid-styled-content`. Do not repeat your own defaults there.
- Store values as a flat map (`foo.bar: 'value'`). The tree form is still
  read, but the settings editor writes a map (Important-106894).

### Enums and their labels

Map style (Feature-104794) with literal labels:

```yaml
settings:
  my.enumSetting:
    label: 'My setting with options'
    type: string
    enum:
      valueA: 'Label of value A'
      valueB: 'Label of value B'
```

List style with labels derived from the set's `labels.xlf` (Feature-106640,
14.2). The key is `settings.<settingKey>.enum.<enumValue>`:

```yaml
settings:
  my.enumSetting:
    type: string
    default: optionA
    enum:
      - optionA
      - optionB
```

```xml
<trans-unit id="settings.my.enumSetting.enum.optionA">
  <source>Option A (localized)</source>
</trans-unit>
```

In map style an `LLL:` value is resolved, a literal stays as-is, a key-only
entry falls back to the enum value and `''` stays empty. Set label files are
mapped to the translation domain `<extension>.sets.<set>` (Feature-93334, 14.0).

## TypoScript, TSconfig and load order

Sources: Feature-103439, Feature-103522 (13.1).

- `setup.typoscript` and `constants.typoscript` next to `config.yaml` are
  inserted like a `static_file_include`.
- A site itself may also hold `setup.typoscript`, `constants.typoscript` and
  `page.tsconfig` next to `config/sites/<site>/config.yaml`. Site TypoScript
  always starts a new scope ("root" and "clear"), dependencies load first, so
  the site can override set TypoScript.
- `sys_template` records still load, but after the site TypoScript, and are
  optional.
- Set constants are always overruled by site settings of the same name. This
  is the bridge for v12/v13 dual support: v12 reads the constant, v13+ the
  setting.
- `ext_typoscript_setup.typoscript` and `ext_typoscript_constants.typoscript`
  are **not** loaded for set-based sites. If global TypoScript is unavoidable,
  use `ExtensionManagementUtility::addTypoScriptSetup()` in `ext_localconf.php`.
- Use `@import` only for files inside the same set.
- A constant can fall back from an old to a new name with
  `{$config.oldThing ?? $myext.thing}` (Feature-103671, 13.1).

## Route enhancers in sets (v14.1+)

Source: Feature-107837 (14.1).

`route-enhancers.yaml` next to `config.yaml` must have a top-level
`routeEnhancers` key and may use `imports`:

```yaml
imports:
  - { resource: 'route-enhancers/*.yaml', glob: true }

routeEnhancers:
  MyEnhancer:
    type: Simple
    routePath: '/my-path/{param}'
    aspects:
      param:
        type: StaticValueMapper
        map:
          value1: '1'
          value2: '2'
```

Merge rules: sets merge in dependency order, later sets override earlier ones;
the site's own `routeEnhancers` always win. Scalars from the site override,
new keys are appended. An invalid file skips the set and is logged.
`typo3/seo-sitemap` ships the sitemap enhancers this way, so sites using that
set no longer configure them by hand.

## Site settings editor

Source: Feature-104794 (13.3).

- Backend module "Site Management > Settings" (administrators only in 13.3).
- Writes only values that differ from the set defaults into
  `config/sites/<site>/settings.yaml`.
