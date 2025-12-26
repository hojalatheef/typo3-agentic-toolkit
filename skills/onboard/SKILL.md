---
name: onboard
description: Onboard a TYPO3 project or extension for Claude Code - generate a project CLAUDE.md (stack, commands, conventions, v14 concepts in use) and a .claude/settings.json permission allowlist for ddev, composer and the TYPO3 console, based on the detected TYPO3 version and tooling. User-only command; run /onboard once per repository.
argument-hint: "[--dry-run]"
disable-model-invocation: true
allowed-tools: Read, Grep, Glob, Write, Edit, Bash(ls:*), Bash(cat:*), Bash(jq:*), Bash(git:*), Bash(ddev describe:*), Bash(composer show:*)
---

# Onboard a TYPO3 repository

Create two files that make every later session faster and safer: `CLAUDE.md` (what this project is and how to work in it) and `.claude/settings.json` (which commands may run without asking). With `--dry-run` in `$ARGUMENTS`, print both files instead of writing them.

## Detected facts

Project type and TYPO3 version:
