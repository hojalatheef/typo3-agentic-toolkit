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
