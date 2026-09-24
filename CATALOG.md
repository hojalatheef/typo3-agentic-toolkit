# Catalog

Every component of the plugin, by category. Skills are invoked as
`/typo3-agentic-toolkit:<name>` or load by themselves when the task (or a file
matching their `paths`) fits. Agents are delegated to by name.

## Build skills — TYPO3 v13/v14 concepts

| Skill | What it builds | Key changelog entries | Activates on |
| --- | --- | --- | --- |
| `site-sets` | Site sets, typed settings, settings editor, route enhancers in sets | Feature-103437, 103439, 103522, 104794, 106640, 107837 | `Configuration/Sets/**`, `Configuration/TypoScript/**` |
| `sitepackage` | v14 site package on PAGEVIEW, Camino-style theme, page title/meta ViewHelpers | Feature-103504, 108539, 107358, 107359, 107056 | `Resources/Private/PageView/**` |
| `fluid-components` | Component collections, slots, typed arguments, XSD autocomplete | Feature-108508, 108524, 109114, 108166, 108229 | `Resources/Private/Components/**` |
| `record-rendering` | Record objects, content areas, record and text rendering ViewHelpers | Feature-103783, 103581, 108726, 108868, 108819 | content element templates |
| `schema-api` | TCA written for v14, read through `TcaSchemaFactory` | Feature-104002, 108027, 107281, 108581, 107889 | `Configuration/TCA/**` |
| `content-blocks` | Content elements with friendsoftypo3/content-blocks (extension, not core) | — | `ContentBlocks/**` |
