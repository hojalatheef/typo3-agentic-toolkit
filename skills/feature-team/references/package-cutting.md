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
| CLI | `Classes/Command/**` | `scheduler-console` |
| Wiring | `ext_localconf.php`, `ext_tables.php`, `Configuration/Services.yaml`, `Configuration/Sets/**`, `composer.json` | `site-sets` |

## Rules

1. Every shared registration file goes to the Wiring package, which runs after the others are merged.
2. A new class and the template that renders it may sit in different packages, but the plan must fix the contract between them (variable names, component arguments, action names) before the fan-out.
3. Tests are not part of implementer packages; `test-engineer` owns `Tests/`.
4. If a cut leaves a package with one tiny file, merge it into a neighbour.

## Signs of a bad cut

- Two implementers report NEEDS-OUTSIDE for the same file with different changes.
- A merge conflicts.
