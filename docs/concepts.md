# Claude Code concepts in this plugin

The plugin doubles as a worked example: each Claude Code building block of the
last year is used at least once, on a real TYPO3 task. This page maps the
concept to the file that shows it.

## Skills

| Concept | Where |
| --- | --- |
| Model-invoked skill with a trigger-rich `description` | every `skills/*/SKILL.md` |
| Progressive disclosure: `references/` and `templates/` loaded on demand | `site-sets`, `fluid-components`, `functional-tests` |
| Auto-activation by file globs (`paths`) | `schema-api` (`Configuration/TCA/**`), `docs-rst` (`Documentation/**`) |
| Dynamic context with `` !`command` `` before the model reads the skill | `review` (diff stat), `onboard` (version), `explain-change` (lookup) |
| Named `arguments` and `$ARGUMENTS` | `explain-change`, `feature-team` |
| Per-skill `model` and `effort` | `explain-change` (Haiku, low) |
| Forked context (`context: fork`) running in a chosen agent | `review` → `code-reviewer`, `adr` |
| User-only commands (`disable-model-invocation`) | `release`, `onboard` |
| Scoped tool permissions (`allowed-tools`) | `quality-gate`, `explain-change` |

## Subagents

| Concept | Where |
| --- | --- |
| Model routing: Haiku sweeps, Sonnet builds, Opus plans and judges | `agents/*.md` |
| `effort` per agent | `typo3-architect`, `code-reviewer`, `security-auditor` |
| Persistent agent memory (`memory: project`) | `typo3-architect` |
| Worktree isolation for parallel writers | `implementer` |
| Background agents | `ci-watcher` |
| Preloaded skills in an agent | `implementer`, `test-engineer`, `docs-writer` |
| Fixed reply formats so the caller can merge results | every agent |
| Resuming an agent with SendMessage for the fix loop | `feature-team` |
| Agent teams (experimental) | `feature-team/references/agent-teams.md` |

## Hooks

| Concept | Where |
| --- | --- |
| `SessionStart` context injection | `hooks/scripts/session-context.sh` |
| `PreToolUse` allow/ask/deny with reasons | `guard-bash.sh`, `guard-files.sh` |
| `PostToolUse` feedback via `additionalContext` | `php-lint.sh`, `legacy-api-check.sh` |
| `FileChanged` with a matcher | `composer.lock` re-detection |
