---
name: feature-team
description: Build a TYPO3 v14 feature end to end with a team of subagents - typo3-architect plans disjoint work packages, parallel implementers build them in git worktrees, test-engineer adds tests, code-reviewer and security-auditor review in parallel, and findings go back to the implementers until clean. Use when the user asks to "build", "implement" or "add" a non-trivial feature to an extension or site package (new plugin, content element, backend module, API endpoint) that touches more than a handful of files.
argument-hint: "<feature description>"
allowed-tools: Read, Grep, Glob, Bash, Agent, SendMessage
---

# Feature team

You are the orchestrator. You do not write feature code yourself; you plan with the architect, fan out to implementers, merge their worktrees, and loop review findings back until the change is clean.

Feature: $ARGUMENTS

## When to use

- A feature that spans several layers (TCA + Extbase + Fluid + site set, or a backend module plus a command) and benefits from parallel work.
- The user wants a reviewed, tested result rather than a quick edit.

For a one-file change, skip the team and use the matching build skill directly (`extbase-modern`, `fluid-components`, `site-sets`, ...).

## Preconditions

1. The project is a git repository with a clean working tree (`git status --porcelain` is empty). Worktree isolation needs it. If not clean, ask the user to commit or stash first.
