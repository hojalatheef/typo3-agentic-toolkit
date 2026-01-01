# .claude/settings.json template

Permission rules use the `Tool(specifier)` syntax. `allow` runs without asking, `ask` always asks, `deny` blocks. Deny wins over ask, ask wins over allow.

## Template for a DDEV project

```json
{
  "permissions": {
    "allow": [
      "Bash(ddev describe)",
      "Bash(ddev composer validate:*)",
      "Bash(ddev composer show:*)",
```
