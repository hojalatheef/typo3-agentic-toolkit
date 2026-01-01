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
      "Bash(ddev composer install)",
      "Bash(ddev typo3 cache:flush:*)",
      "Bash(ddev typo3 lint:yaml:*)",
      "Bash(ddev typo3 fluid:analyze:*)",
      "Bash(ddev typo3 list:*)",
      "Bash(ddev exec vendor/bin/phpstan:*)",
      "Bash(ddev exec vendor/bin/php-cs-fixer:*)",
```
