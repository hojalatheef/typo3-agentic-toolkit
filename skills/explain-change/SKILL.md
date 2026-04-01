---
name: explain-change
description: Explain what changed in TYPO3 about a topic, API, class, ViewHelper or changelog ID, using only the official core changelog rst files and citing every entry by ID and version. Use when the user asks "what changed about X in TYPO3 14", "why is X deprecated", "explain Feature-108508", "since when does TYPO3 have X" or "is X available in 13.4".
argument-hint: "<topic | changelog id>"
arguments:
  - topic
model: haiku
effort: low
allowed-tools: Read, Grep, Glob, Bash(t3-changelog:*), Bash(grep:*), Bash(ls:*)
---

# Explain a TYPO3 change

Topic: $topic

Answer only from the changelog. If nothing matches, say "not found in the TYPO3 13/14 changelog" and suggest a better keyword; never fill the gap from memory.

## Lookup

Initial search results:

!`t3-changelog "$topic" 2>/dev/null | head -40 || true`

If that printed nothing (tool missing or no hit), search directly:

```bash
grep -rli "<keyword>" ~/.cache/typo3-extension-migration/typo3-core/typo3/sysext/core/Documentation/Changelog/1[34]* | head -30
```
