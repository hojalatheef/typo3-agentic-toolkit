---
max_turns: 20
allowed_tools: [Read, Glob, Grep, Write, Edit, Bash, Skill]
---

Bots keep hammering our contact form (ContactController::submitAction). Limit it to 3 submissions per IP every 10 minutes and show a friendly message when the limit is hit. TYPO3 14.3.
