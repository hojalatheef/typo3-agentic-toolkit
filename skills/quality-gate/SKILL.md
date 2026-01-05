---
name: quality-gate
description: Run the full quality gate for a TYPO3 extension or project - composer validate, php -l, php-cs-fixer with the TYPO3 coding standards, PHPStan, Rector dry-run, typo3 lint:yaml, typo3 fluid:analyze, unit and functional tests - and report a per-step verdict taken from real command output. Use before saying a feature is done, before a merge or release, or when the user asks "is it green", "run the checks" or "run the gate".
argument-hint: "[path] [--fast]"
allowed-tools: Read, Grep, Glob, Bash
---

# Quality gate

A step passes only when its command ran and exited 0 here, now. A step that could not run is SKIPPED, never PASSED. Numbers in the report come from the output, never from memory.

Target: $ARGUMENTS (default: the current directory). With `--fast`, run steps 1 to 6 only and say so in the report.

## Environment

Detected tools:

!`for t in ddev composer php docker; do printf '%s: ' "$t"; command -v "$t" >/dev/null 2>&1 && echo yes || echo no; done; ls vendor/bin 2>/dev/null | grep -E '^(php-cs-fixer|phpstan|rector|phpunit|typo3)$' | tr '\n' ' ' || true`

Pick the runner once and use it for every step:

1. `.ddev/` exists and `ddev describe` works: prefix commands with `ddev exec` (TYPO3 CLI via `t3`, which is DDEV-aware).
2. Host `php` works (`php -v` exits 0): run directly.
