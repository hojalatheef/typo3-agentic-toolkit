# Cutting disjoint work packages

Parallel implementers only work when no two of them touch the same file. These cuts work well for TYPO3 extensions.

## Typical packages

| Package | Files | Build skill |
| --- | --- | --- |
| Schema | `Configuration/TCA/*.php`, `Configuration/TCA/Overrides/*.php`, `ext_tables.sql` | `schema-api`, `content-blocks` |
| Domain | `Classes/Domain/**` | `extbase-modern` |
| Controller | `Classes/Controller/**` | `extbase-modern` |
| Rendering | `Resources/Private/**`, `Configuration/Fluid/ComponentCollections.php` | `fluid-components`, `record-rendering` |
| Labels | `Resources/Private/Language/**` | `translations` |
| Backend | `Classes/Backend/**`, `Configuration/Backend/*.php` | `backend-modules` |
