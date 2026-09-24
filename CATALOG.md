# Catalog

Every component of the plugin, by category. Skills are invoked as
`/typo3-agentic-toolkit:<name>` or load by themselves when the task (or a file
matching their `paths`) fits. Agents are delegated to by name.

## Build skills — TYPO3 v13/v14 concepts

| Skill | What it builds | Key changelog entries | Activates on |
| --- | --- | --- | --- |
| `site-sets` | Site sets, typed settings, settings editor, route enhancers in sets | Feature-103437, 103439, 103522, 104794, 106640, 107837 | `Configuration/Sets/**`, `Configuration/TypoScript/**` |
