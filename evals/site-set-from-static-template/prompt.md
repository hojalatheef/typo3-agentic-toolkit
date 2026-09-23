---
max_turns: 25
allowed_tools: [Read, Glob, Grep, Write, Edit, Bash, Skill]
---

This extension still ships its TypoScript as a static template (see Configuration/TCA/Overrides/sys_template.php). We're on TYPO3 14.3 with site sets everywhere else. Convert it into a site set so integrators can add it to a site's dependencies, and keep the two constants configurable.
