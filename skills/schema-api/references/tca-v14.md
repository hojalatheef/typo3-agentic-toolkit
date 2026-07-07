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
