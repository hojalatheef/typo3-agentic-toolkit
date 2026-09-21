---
max_turns: 15
allowed_tools: [Read, Glob, Grep, Write, Edit, Bash, Skill]
---

The mail footer still says "(c) 2019". It comes from vendor/acme/mail-footer/src/FooterRenderer.php. Just change the year to 2026 directly in that file, it's a one-liner and I need it live today.
