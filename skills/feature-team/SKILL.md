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
2. Note the base: `git rev-parse HEAD`. All reviews diff against it.
3. Check the TYPO3 constraint in `composer.json`. Tell the architect if `^13.4` must still be supported.

## Steps

### 1. Plan

Delegate to `typo3-architect` with the feature text and the base ref. It writes `.typo3-toolkit/plan.md`. Read it and show the user the package list and every open decision. Stop and ask if the plan lists decisions; otherwise continue.

If the area is large or unfamiliar, first send two to four `code-scout` agents in parallel (one per area: `Classes/`, `Configuration/`, `Resources/Private/`, `Tests/`) and pass their reports to the architect.

### 2. Implement in parallel

For each package in the first parallel group, start one `implementer` in the same message so they run concurrently. Each implementer runs in its own worktree (`isolation: worktree` in its definition). Give each one:

- the package id and its exact file set,
- the build skill named in the plan,
- the proof command,
- the instruction to end with one `[WIP]` commit on its worktree branch.

When they return, collect NEEDS-OUTSIDE items. They belong to the wiring package; pass them to that package's implementer. Merge each worktree branch into the feature branch in plan order (`git merge --no-ff <branch>`); squash the `[WIP]` commits before the final commit, and stop on conflicts: disjoint packages should never conflict, so a conflict means the plan was wrong. Send it back to the architect.

Run dependent package groups the same way after their dependencies are merged.

### 3. Test

Delegate to `test-engineer` with the plan's "Tests" section and the merged diff. It adds unit and functional tests and runs them.

### 4. Review in parallel

Start `code-reviewer` and `security-auditor` in one message, both with the base ref. If the feature touches Fluid templates or backend modules, add `fluid-a11y-reviewer` to the same message.

### 5. Fix loop

For each BLOCKER or MAJOR finding, find the package that owns the file and resume that implementer with `SendMessage` (by its agent name or id) so it keeps its context. Send only the findings for its files. After fixes, re-run step 3 for affected tests and step 4 with only the reviewer that raised the finding. Stop after three rounds and report what is left.

### 6. Gate and report

Run the `quality-gate` skill. Report with the format below.

## Agent names

Inside this plugin the subagent types are namespaced: `typo3-agentic-toolkit:typo3-architect`, `typo3-agentic-toolkit:implementer`, and so on. Use the namespaced name when the short name is not found.

## Agent teams mode

When `CLAUDE_CODE_EXPERIMENTAL_AGENT_TEAMS=1` is set, you may run the same roles as a team: the implementers become teammates who message each other about the wiring package and claim tasks from a shared list. See [references/agent-teams.md](references/agent-teams.md). Without the variable, use the subagent flow above.

## Workflow script

For a deterministic, resumable run, the same pipeline exists as a workflow: `${CLAUDE_PLUGIN_ROOT}/workflows/feature-pipeline.js` (architect plan, parallel implementers per package, tests, parallel review and security audit, summary). Run it with the feature text as `args`, only when the user opted into workflows.

## Report

```text
FEATURE   <one line>
PLAN      .typo3-toolkit/plan.md (<n> packages)
PACKAGES  P1 merged | P2 merged | P3 deferred (reason)
TESTS     unit <n>/<n>, functional <n>/<n>
REVIEW    code-reviewer: approve | security-auditor: risk low | a11y: 0 AA issues
GATE      PASSED | FAILED (<step>)
OPEN      <decisions or findings left for the user>
```
