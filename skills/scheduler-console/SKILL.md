---
name: scheduler-console
description: >-
  Build TYPO3 v14 console commands, scheduler tasks and message handlers - native TCA table tx_scheduler_task with custom TCA record types instead of AdditionalFieldProviders, timing options via overrideFieldTca, task priority, #[AsNonSchedulableCommand], the new task wizard event, configuration:show/set/remove, messenger:consume options, shell completion and #[AsMessageHandler]. Use when writing a Symfony command, a scheduler task, a queue handler, or when the user asks to "make this schedulable", "add a scheduler task with fields", "migrate an AdditionalFieldProvider" or "run the message queue from cron".
argument-hint: "[command|task|handler] [extension path]"
allowed-tools: Read, Grep, Glob, Edit, Write, Bash(*/bin/typo3 list*), Bash(*/bin/typo3 help *), Bash(*/bin/typo3 configuration:show*), Bash(ddev exec*typo3 list*)
paths:
  - "**/Classes/Command/**/*.php"
  - "**/Classes/Task/**/*.php"
  - "**/Classes/**/*Handler.php"
  - "**/Configuration/TCA/Overrides/tx_scheduler_task*.php"
---

# Console commands, scheduler tasks and messages (TYPO3 v14)

Arguments: `$ARGUMENTS`.

In v14 the scheduler stores tasks as a normal TCA table (Feature-106739, 14.0). A task with its own fields is a
TCA *record type* of `tx_scheduler_task` (Feature-107526, 14.0); FormEngine and DataHandler do validation and
persistence. Symfony commands are schedulable by default; opt out with an attribute (Feature-107151, 14.0).

## When to use

- Batch work that an admin should run on a schedule: prefer a Symfony command (`#[AsCommand]`). It is
  automatically offered by the scheduler as "Execute console commands".
- A task needs typed configuration fields edited in the backend that a command argument cannot express well:
  write an `AbstractTask` subclass registered as a TCA record type.
- An existing task uses `AdditionalFieldProviderInterface`, a serialized task object
  or `$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['frequencyOptions']` (removed, Breaking-107488).
- A command must never be scheduled (interactive, destructive, long-running import): `#[AsNonSchedulableCommand]`.
- Work should leave the request: dispatch a message, handle it with `#[AsMessageHandler]` (Feature-101700, 13.0),
  consume it with `messenger:consume` (Feature-106261, 14.2).

## Steps

1. Command: copy [templates/ImportCommand.php](templates/ImportCommand.php). Use `#[AsCommand]`, constructor
   injection, `Command::SUCCESS` / `Command::FAILURE`. Add completion callbacks to arguments
   (Feature-106839). Add `#[AsNonSchedulableCommand]` only when scheduling would be harmful.
2. Task with fields: copy [templates/CleanupTask.php](templates/CleanupTask.php) and
   [templates/tx_scheduler_task_cleanup.php](templates/tx_scheduler_task_cleanup.php) (TCA override). Implement
   `execute()`, `getTaskParameters()`, `setTaskParameters()` and optionally `getAdditionalInformation()`.
   New columns added with `addTCAcolumns()` are created by the database analyzer from TCA.
