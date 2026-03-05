# Scheduler tasks as TCA records

## Table structure (Feature-106739, 14.0)

`tx_scheduler_task` now has TCA. Custom data moved from a serialized object into:

- `tasktype` - the task class name or the CLI command name,
- `parameters` - a `json` column for tasks without own columns,
