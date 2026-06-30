# Scheduler tasks as TCA records

## Table structure (Feature-106739, 14.0)

`tx_scheduler_task` now has TCA. Custom data moved from a serialized object into:

- `tasktype` - the task class name or the CLI command name,
- `parameters` - a `json` column for tasks without own columns,
- `execution_details` - a `json` field holding the timing (Feature-107488),
- `priority` - integer, 14.2 (Feature-109110).

Consequences: editing through FormEngine, saving through DataHandler (hooks, history, audit), import/export
support, restore through the recycler, automatic restrictions and TCA schema. An upgrade wizard migrates existing
rows.

## Custom TCA record types (Feature-107526, 14.0)

Steps from the changelog:

1. Task class extends `TYPO3\CMS\Scheduler\Task\AbstractTask`, logic in `execute()` (return `true` on
   success).
2. Implement `getTaskParameters(): array` (called during migration to read old serialized values; return
   column => value) and `setTaskParameters(array $parameters): void` (receives the full database record for
   TCA-based tasks).
3. Optional `getAdditionalInformation()` for the module list.
4. TCA in `Configuration/TCA/Overrides/tx_scheduler_task_<name>.php`.
5. New columns via `ExtensionManagementUtility::addTCAcolumns('tx_scheduler_task', [...])`.
