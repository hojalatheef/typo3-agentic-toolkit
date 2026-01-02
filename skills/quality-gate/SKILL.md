---
name: quality-gate
description: Run the full quality gate for a TYPO3 extension or project - composer validate, php -l, php-cs-fixer with the TYPO3 coding standards, PHPStan, Rector dry-run, typo3 lint:yaml, typo3 fluid:analyze, unit and functional tests - and report a per-step verdict taken from real command output. Use before saying a feature is done, before a merge or release, or when the user asks "is it green", "run the checks" or "run the gate".
argument-hint: "[path] [--fast]"
allowed-tools: Read, Grep, Glob, Bash
---

# Quality gate

A step passes only when its command ran and exited 0 here, now. A step that could not run is SKIPPED, never PASSED. Numbers in the report come from the output, never from memory.
