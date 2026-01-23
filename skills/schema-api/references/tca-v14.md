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
