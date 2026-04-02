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

Try the class short name, the ViewHelper name without namespace, or the CLI command. A numeric topic is an issue ID: `ls .../Changelog/*/*-<id>-*.rst`.

## Steps

1. Pick the entries that actually answer the topic (usually one to three). Prefer Feature and Breaking over Important.
2. Read each chosen rst in full.
3. Look for later entries about the same API (a Deprecation in 14.x of a 13.x Feature, a Breaking that removes it).
4. Write the answer in the format below.

## Answer format

```text
<Topic> - short answer in one or two sentences.

What changed
- <fact from the rst> (Feature-<id>, <version>)
- <fact> (Deprecation-<id>, <version>)

How to use it
<one short code sample copied or trimmed from the rst, with the language fence>

Availability
TYPO3 13.4: yes/no | TYPO3 14.x: since 14.<n>

Sources
- <version>/<rst file name>
```

## Rules

- The folder name of the rst (`13.1`, `14.2`) is the version that introduced the change.
- Quote names exactly: class names, attribute names, YAML keys, CLI commands.
- Keep the code sample shorter than 20 lines and do not invent parts that are not in the rst.
- For a deep dive or many related entries, suggest delegating to the `changelog-researcher` agent.
