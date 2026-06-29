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
