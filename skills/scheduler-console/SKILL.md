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
3. Register the task with `ExtensionManagementUtility::addRecordType()` on table `tx_scheduler_task`
   (`value` = task class, `group`, `icon`, `iconOverlay`). The wizard shows it automatically (Feature-107471).
4. Timing: add frequency presets via `execution_details` `overrideFieldTca` (Feature-107488). Priority levels
   are TCA items of the `priority` column (Feature-109110, 14.2).
5. Messages: copy [templates/ImportMessage.php](templates/ImportMessage.php) and
   [templates/ImportMessageHandler.php](templates/ImportMessageHandler.php); configure the
   transport routing in `Services.yaml` / `TYPO3_CONF_VARS` as before; schedule
   `messenger:consume <receiver> --time-limit=<below cron interval>`.
6. After migrating legacy tasks run the upgrade wizard "Migrate the contents of the tx_scheduler_task database
   table into a more structured form".
7. Details: [references/scheduler-tca.md](references/scheduler-tca.md),
   [references/cli-and-messaging.md](references/cli-and-messaging.md).

## Checklist

- [ ] Commands use `#[AsCommand]`, not `Services.yaml` `console.command` tags.
- [ ] `schedulable: false` tags replaced by `#[AsNonSchedulableCommand]` (14.0+ only).
- [ ] Task class extends `TYPO3\CMS\Scheduler\Task\AbstractTask`; `execute()` returns `bool`.
- [ ] `setTaskParameters()` accepts both the old serialized keys and the new column names (migration path).
- [ ] No `AdditionalFieldProvider` left; fields are TCA columns with labels, `eval`/`required` validation.
- [ ] Task TCA `showitem` keeps `tasktype`, `task_group`, `description`, the `execution` palette and `disable`.
