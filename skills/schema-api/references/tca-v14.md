# TCA for v14: option by option

Every snippet follows the cited changelog entry. Labels use the v14 short
translation-domain syntax where the changelog does (`my_extension.db:...`);
`LLL:EXT:...xlf:key` keeps working.

## Less boilerplate: columns, SQL, tabs

Auto-created columns from `ctrl` (Feature-104311, 13.3). Set in the base file
only; the core adds `columns` for:

| `ctrl` key | Column created |
| --- | --- |
| `enablecolumns.disabled` | check toggle |
| `enablecolumns.starttime` / `endtime` | `datetime` |
| `enablecolumns.fe_group` | select of `fe_groups` |
| `editlock` | check toggle, admins only |
| `descriptionColumn` | `text` |
| `languageField` (+ `transOrigPointerField`, defaults to `l10n_parent`) | `language` + parent select |
| `transOrigDiffSourceField` | `passthrough` |
| `translationSource` | `passthrough` |

Load order: base files, then auto-created columns, then Overrides, then
migrations. To change a default, override single properties in
`Configuration/TCA/Overrides/` - for example
`$GLOBALS['TCA']['pages']['columns']['hidden']['config']['default'] = 1;`.

Auto-created DB fields (Feature-101553, 13.0): fields of nearly every TCA type
(`input`, `text`, `check`, `select`, `group`, `inline`, `file`, `link`,
`number`, `email`, `color`, `flex`, ...) get a database column without
`ext_tables.sql`. Explicit SQL still wins. Before removing SQL lines, make
sure `nullable` in TCA matches the data; the core wizard "Migrate NULL field
values to DEFAULT values" fixes rows.

System tabs for content types (Feature-104814, 13.3): `General`, `Language`,
`Access` and `Notes` are added from `ctrl`; `showitem` lists only your fields.
Start with `--div--` to omit the General tab. Custom labels for system fields
are no longer preserved.

```php
'slider' => [
    'showitem' => '
            --palette--;;headers,
            slider_elements,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
            categories,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
    ',
],
```

## Per-type configuration

Type-specific `title` and `previewRenderer` (Feature-108027, 14.0):

```php
'types' => [
    'article' => [
        'title' => 'my_extension.db:tx_my_table.type.article',
        'showitem' => 'title, content, author',
    ],
    'event' => [
        // falls back to ctrl['title']
        'showitem' => 'title, content, event_date',
    ],
],
```

Type-specific labels (Feature-108581, 14.2) - used by
`BackendUtility::getRecordTitle()` (lists, page tree, history); FormEngine does
not use it yet:

```php
'types' => [
    'article' => ['label_alt' => 'teaser'],
    'event' => ['label_alt' => 'event_date,location', 'label_alt_force' => true],
],
```

The same via `ExtensionManagementUtility::addRecordType($item, $showitem, $additionalTypeInformation)`:

```php
ExtensionManagementUtility::addRecordType(
    [
        'label' => 'LLL:frontend.ttc:CType.shortcut',
        'value' => 'my-type',
        'icon' => 'my-icon',
        'group' => 'special',
    ],
    'my-header',
    [
        'label' => 'header',
        'label_alt' => 'records',
    ]
);
```

Type-specific defaults in page or user TSconfig (Feature-107281, 14.0). They
apply only when the type is known at creation (wizard `defVals` or the type
field in the DataHandler datamap):

```typoscript
TCAdefaults.tt_content {
    header_layout = 1
    header_layout.types.textmedia = 3
    frame_class.types.textmedia = ruler-before
}
```

## Field options

`itemsProcessors` (Feature-107889, 14.0) - ordered by key, each class
implements `TYPO3\CMS\Core\DataHandling\ItemsProcessorInterface` and returns a
`SelectItemCollection`. If `itemsProcFunc` is also set, it runs first.

```php
'itemsProcessors' => [
    100 => [
        'class' => SpecialRelationsProcessor::class,
        'parameters' => ['foo' => 'bar'],
    ],
],
```

TSconfig can pass parameters with the same key:
`TCEFORM.example_table.content.itemsProcessors.100.foo = bar`.

TCA type `country` (Feature-99911, 14.0):

```php
'country' => [
    'label' => 'Country',
    'config' => [
        'type' => 'country',
        'labelField' => 'localizedName',
        'prioritizedCountries' => ['AT', 'CH'],
        'sortItems' => ['label' => 'asc'],
        'filter' => ['onlyCountries' => ['DE', 'AT', 'CH']],
        'default' => 'DE',
        'required' => false,
    ],
],
```

Stores the ISO alpha-2 code; Extbase maps it to `TYPO3\CMS\Core\Country\Country`.

`valuePicker` items with `label`/`value` keys (Feature-106092, 14.0).

`searchable` (Feature-106972, 14.0): `ctrl.searchFields` is removed; all
fields of type `color`, `datetime`, `email`, `flex`, `input`, `json`, `link`,
`slug`, `text`, `uuid` are searched unless `'searchable' => false`.

`relationship` (Feature-103581, 13.3): `oneToOne` / `manyToOne` on relation
types makes Record objects return one object instead of a collection.

## Page types and plugins

Page types (Feature-108557, Feature-97898, 14.2) - replaces
`PageDoktypeRegistry->add()` in `ext_tables.php` (deprecated):

```php
$GLOBALS['TCA']['pages']['types']['116']['allowedRecordTypes'] = ['tt_content', 'my_custom_record'];
$GLOBALS['TCA']['pages']['types']['116']['isViewable'] = false;
```

Default allowed tables are `pages`, `sys_category`, `sys_file_reference`,
`sys_file_collection` plus tables with `ctrl.security.ignorePageTypeRestriction`.

Plugin with FlexForm in one call (Feature-107047, 14.0); `addPiFlexFormValue()`
