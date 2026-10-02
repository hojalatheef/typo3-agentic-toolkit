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
