# Agents

Subagents live in `agents/<name>.md`. Inside the plugin their type is
namespaced: `typo3-agentic-toolkit:<name>`. Use the namespaced name when the
short one is not found.

Every agent states its `model` on purpose, read-only agents have no `Edit`
or `Write`, and every agent ends with a fixed reply format so the caller
can merge results. Plugin agents cannot declare `hooks`, `mcpServers` or
`permissionMode`; the self-test fails if one does.

## Overview

| Agent | Model | Effort | Writes | Special | Preloaded skills |
| --- | --- | --- | --- | --- | --- |
| `typo3-architect` | Opus | high | `.typo3-toolkit/plan.md` only | `memory: project`, `maxTurns: 40` | - |
| `code-scout` | Haiku | - | no | `maxTurns: 25` | - |
| `changelog-researcher` | Haiku | - | no | `maxTurns: 20` | - |
| `implementer` | Sonnet | - | its package files | `isolation: worktree`, `maxTurns: 80` | `site-sets`, `schema-api`, `extbase-modern`, `fluid-components`, `record-rendering`, `translations`, `system-resources` |
| `test-engineer` | Sonnet | - | `Tests/`, test config | `maxTurns: 60` | `functional-tests` |
| `code-reviewer` | Opus | high | no | `maxTurns: 40` | - |
| `security-auditor` | Opus | high | no | `maxTurns: 40` | `security-hardening` |
| `fluid-a11y-reviewer` | Sonnet | - | no | `maxTurns: 40` | - |
| `docs-writer` | Sonnet | - | `Documentation/`, `README.md` | `maxTurns: 50` | `docs-rst` |
| `ci-watcher` | Haiku | - | no | `background: true`, `maxTurns: 40` | - |

## typo3-architect

Designs a feature on current core concepts and writes the plan. It never
edits code, configuration or tests.

- **Tools**: Read, Grep, Glob, Bash, Write.
- **Memory**: `memory: project`. Reads past plans and conventions first and
  records new decisions at the end.
- **Called by**: `feature-team`, `workflows/feature-pipeline.js`, and again
  when an implementer reports a surprise.
- **Inputs**: the feature text, the base ref, optional `code-scout`
  reports. Reads `composer.json`, `ext_emconf.php`, `Configuration/`,
  `Classes/`, `Resources/Private/`, `Tests/`.
- **Design rules**: target v14 (14.3) with PHP 8.2+, or only 13.4 concepts
  when `^13.4` is still allowed; prefer the current concept and confirm its
  changelog entry with `t3-changelog`; point each package at a build skill;
  disjoint file sets with one wiring package; a proof command per package;
  three to six packages.
- **Plan file**: see [architecture.md](architecture.md#the-plan-contract).

```text
PLAN      .typo3-toolkit/plan.md
PACKAGES  <n> (<parallel group 1: P1,P2,P3> then <P4>)
CONCEPTS  <concept (Feature-id)>, ...
DECISIONS <question needing the user> | none
```

## code-scout

Fast read-only sweep of one area (`Classes/Controller`,
`Configuration/TCA`, `Resources/Private`, ...). Finds legacy patterns that a
v14 concept replaces and the existing code a feature must fit into.

- **Tools**: Read, Grep, Glob, Bash.
- **Called by**: `feature-team` (two to four in parallel before planning),
  `ticket-analysis`, `workflows/extension-audit.js`.
- **Inputs**: one area and optionally a specific question ("where are
  plugins registered").
- **Rules**: confirms each grep hit by opening the file; skips `vendor/`,
  `var/`, `public/typo3/`, `node_modules/`; sends anything outside its
  pattern table to `changelog-researcher` instead of guessing.

```text
AREA   <path>
HIT    <file>:<line> | <pattern> | <current concept (Feature-id)>
FACT   <file>:<line> | <neutral observation the planner needs>
NONE   <pattern searched with no hits>
```

## changelog-researcher

Answers "what changed about X" strictly from the core changelog rst files.

- **Tools**: Read, Grep, Glob, Bash.
- **Called by**: `explain-change` (for deep dives), `ticket-analysis`,
  `workflows/extension-audit.js` (ID verification), any agent that needs a
  fact checked.
- **Rules**: reads every cited rst in full, checks for follow-up
  deprecations or removals, separates what the rst says from inference,
  says when something exists only in 14.x.

```text
QUESTION <restated>
ANSWER   <two to five sentences>
SOURCE   Feature-<id> (<version>) | <rst file name> | <one-line summary>
SOURCE   Breaking-<id> (<version>) | ...
CAVEAT   <version limits, follow-up deprecations, inferences>
```

## implementer

Builds one work package in its own git worktree and proves it.

- **Tools**: Read, Edit, Write, Grep, Glob, Bash.
- **Called by**: `feature-team`, `workflows/feature-pipeline.js`. Resumed
  with `SendMessage` for review findings.
- **Inputs**: package id, exact file set, build skill, proof command.
- **Boundaries**: edits only its file set; changes needed elsewhere go to
  `NEEDS-OUTSIDE`; ends with one `[WIP] <id> <name>` commit; never pushes,
  rebases or touches other branches; no drive-by refactors; never adds
  `@phpstan-ignore`, baseline entries or `markTestSkipped` to get green.
  APIs not shown in a skill are confirmed with `t3-changelog` or deferred.

```text
PACKAGE  <id> <name>
WORKTREE <path> (branch <name>)
CHANGED  <file> | what and why
DEFERRED <file>:<line> | why (needs decision / API not confirmed)
NEEDS-OUTSIDE <file> | exact change
PROOF    <command> -> exit <code> (<one-line result>)
```

## test-engineer

Writes and runs unit and functional tests with `typo3/testing-framework`.

- **Tools**: Read, Edit, Write, Grep, Glob, Bash.
- **Called by**: `feature-team`, `workflows/feature-pipeline.js`,
  `ticket-analysis` recommendations for bugs.
- **Inputs**: the feature or bug, the "Tests" section of the plan, the
  existing test layout.
- **Rules**: one behaviour per test, PHPUnit attributes, CSV fixtures,
  failing test first for bugs; prefers `Build/Scripts/runTests.sh`, then
  `ddev exec vendor/bin/phpunit`, then `vendor/bin/phpunit` with
  `typo3DatabaseDriver=pdo_sqlite`.

```text
ADDED    <test file> | <behaviour covered>
FIXTURE  <csv file> | <tables>
RUN      <command> -> exit <code> | tests <n>, assertions <n>, failures <n>, skipped <n>
GAP      <behaviour not covered and why>
```

## code-reviewer

Adversarial diff review. Assumes the change is subtly wrong.

- **Tools**: Read, Grep, Glob, Bash.
- **Called by**: `review` (forked context), `feature-team`,
  `workflows/feature-pipeline.js`, `workflows/extension-audit.js`
  (synthesis).
- **Inputs**: base ref, `.typo3-toolkit/plan.md` if present, gate output if
  available.
- **Checks in order**: correctness, security (deep issues go to
  `security-auditor`), v14 practice (confirmed with `t3-changelog`),
  silenced checks, public surface, tests. No style nits php-cs-fixer would
  fix.

```text
VERDICT  approve | request changes
BLOCKER  <file>:<line> | problem | evidence | suggested fix
MAJOR    <file>:<line> | ...
MINOR    <file>:<line> | ...
BREAKING <changelog-ready line for users>
```

## security-auditor

Read-only security audit of a diff or a whole extension.

- **Tools**: Read, Grep, Glob, Bash.
- **Called by**: `feature-team`, `workflows/feature-pipeline.js`, or
  directly for a security review.
- **Checklist**: output encoding in Fluid and PHP, QueryBuilder named
  parameters, access control (`#[Authorize]`, backend routes, record
  access), abuse (`#[RateLimit]` on uncached actions), CSP, files and
  secrets. Every finding needs file, line, attack path and fix.

```text
RISK     critical | high | medium | low | none
FINDING  <severity> | <file>:<line> | <issue> | <attack path> | <fix>
OK       <area checked with nothing found>
```

## fluid-a11y-reviewer

Reviews templates, partials, layouts, components and backend templates for
WCAG 2.2 AA and returns exact markup fixes.

- **Tools**: Read, Grep, Glob, Bash.
- **Called by**: `a11y-audit`, `feature-team` and the feature pipeline when
  `Resources/Private` changed.
- **Limits**: contrast and screen reader output are listed as MANUAL items,
  never passed.

```text
SUMMARY  <n> issues (<a> A, <aa> AA), <m> manual checks
ISSUE    <WCAG id> <level> | <file>:<line> | problem | fix (exact markup)
MANUAL   <WCAG id> | <what to test and how>
OK       <criterion checked with nothing found>
```

## docs-writer

Writes reStructuredText documentation and proves it renders.

- **Tools**: Read, Edit, Write, Grep, Glob, Bash.
- **Called by**: `docs-rst` for larger jobs, `release`, or directly.
- **Proof**: renders with the `render-guides` Docker image and fixes every
  warning; without Docker it says so and does not claim the docs render.

```text
CHANGED  <file> | what was documented
RENDER   <command> -> exit <code> | warnings <n>
TODO     <topic not documented and why>
```

## ci-watcher

Background watcher for a CI run or a long local command.

- **Tools**: Bash, Read, Grep.
- **Background**: `background: true`, so the main session keeps working.
- **Inputs**: a GitHub Actions run id or branch, a GitLab pipeline id, or a
  local command plus its log file.
- **Rules**: watches and reports only; never re-runs, cancels or pushes.

```text
RUN      <id or command> | <success | failure | cancelled> | <duration>
JOB      <name> | <result>
FAILURE  <job> | <step> | <file>:<line if known> | <first error line>
EXCERPT  <short log excerpt>
GUESS    <likely cause>
```

## Status line

`settings.json` sets a `subagentStatusLine` that shows, per running agent,
its type, model, tokens in thousands and runtime in seconds, for example
`code-reviewer · opus · 42k tok · 95s`.
