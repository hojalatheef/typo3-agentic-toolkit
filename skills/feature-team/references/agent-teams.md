# Agent teams mode

Agent teams are an experimental Claude Code feature. Teammates are full sessions that share a task list and can message each other, unlike subagents, which only report back to the orchestrator.

## Enable

Set the variable before starting Claude Code, for example in the project `.claude/settings.json`:

```json
{
  "env": {
    "CLAUDE_CODE_EXPERIMENTAL_AGENT_TEAMS": "1"
  }
}
```

Without it, the `feature-team` skill uses plain subagents.

## Roles

| Teammate | Agent definition | Owns |
| --- | --- | --- |
| lead | the main session | plan approval, merges, final gate |
| architect | `typo3-architect` | `.typo3-toolkit/plan.md` |
| builder-N | `implementer` | one package from the plan |
| tester | `test-engineer` | `Tests/` |
| reviewer | `code-reviewer` | findings only |
| auditor | `security-auditor` | findings only |

Keep the team at six teammates or fewer. More teammates mean more merge work and more token use, not a faster result.
