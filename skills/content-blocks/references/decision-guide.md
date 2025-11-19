# Content Blocks or plain TCA?

Content Blocks (`friendsoftypo3/content-blocks`) is an extension, not core.
This guide compares it with the core-only path: TCA in
`Configuration/TCA/Overrides/tt_content.php`, Record objects for rendering
and a site set for TypoScript.

## Comparison

| Aspect | Content Blocks | TCA + site set |
| --- | --- | --- |
| Source of truth | one `config.yaml` per element | TCA PHP arrays, TypoScript, templates |
| Dependency | community extension, versioned separately | core only |
| Database columns | generated from YAML | generated from TCA (core auto-creates most columns) |
