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
6. Register with `ExtensionManagementUtility::addRecordType()`:

```php
ExtensionManagementUtility::addRecordType(
    [
        'label' => 'my_extension.tasks:cleanup.title',
        'description' => 'my_extension.tasks:cleanup.description',
        'value' => \MyVendor\MyExtension\Task\CleanupTask::class,
        'icon' => 'mimetypes-x-tx_scheduler_task_group',
        'iconOverlay' => 'content-clock',
        'group' => 'my_extension',
    ],
    '
        --div--;core.form.tabs:general,
            tasktype,
            task_group,
            description,
            number_of_days;my_extension.tasks:cleanup.days,
        --div--;core.form.tabs:timing,
            --palette--;;execution,
        --div--;core.form.tabs:access,
            disable,
        --div--;core.form.tabs:extended,',
    [
        'columnsOverrides' => [
            'number_of_days' => ['config' => ['eval' => 'required']],
        ],
    ],
    '',
    'tx_scheduler_task'
);
```

`setTaskParameters()` should accept old and new keys so migrated and new records both work:

```php
public function setTaskParameters(array $parameters): void
{
    $this->period = (int)($parameters['RecyclerCleanerPeriod'] ?? $parameters['number_of_days'] ?? 180);
}
```

Core reuses columns such as `number_of_days`, `selected_tables`, `file_storage` across task types; check the
existing TCA (`$GLOBALS['TCA']['tx_scheduler_task']['columns']`) before adding a duplicate column.

## Timing options (Feature-107488, Breaking-107488, 14.0)

`execution_details` exposes `overrideFieldTca` for its sub-fields `frequency`, `runningType`, `multiple`,
