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
