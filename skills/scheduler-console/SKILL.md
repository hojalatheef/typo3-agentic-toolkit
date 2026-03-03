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
