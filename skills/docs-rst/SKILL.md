---
name: docs-rst
description: Write and render TYPO3 extension documentation in reStructuredText - Documentation/ layout with guides.xml, Index.rst, Includes.rst.txt, confval for settings, code-block captions, and rendering with the render-guides Docker image. Delegates larger writing jobs to the docs-writer agent. Use when the user asks to "document this extension", "add docs", "set up Documentation/", "migrate Settings.cfg to guides.xml" or "render the docs".
argument-hint: "[topic or --render]"
allowed-tools: Read, Edit, Write, Grep, Glob, Bash, Agent
paths:
  - "Documentation/**"
---

# TYPO3 documentation in reST

TYPO3 extension documentation lives in `Documentation/`, is written in reStructuredText, configured by `Documentation/guides.xml` and rendered by the `render-guides` tool (phpDocumentor guides with the TYPO3 theme). docs.typo3.org renders it on every push once the extension is registered.

Request: $ARGUMENTS

## When to use

- A new extension has no `Documentation/` yet.
- A feature changed and its settings, site-set options or usage must be documented.
- `Documentation/Settings.cfg` exists: migrate it to `guides.xml`.
- Before a release, to prove the docs render without warnings.

For more than two or three pages of new content, delegate to the `docs-writer` agent with the topic list and let it render and report.

## Layout

```text
Documentation/
  guides.xml
  Includes.rst.txt
  Index.rst
  Introduction/Index.rst
  Installation/Index.rst
  Configuration/Index.rst
  Configuration/SiteSet.rst
  Usage/Index.rst
  Decisions/Index.rst      (optional, see the adr skill)
```

Templates for `guides.xml`, `Index.rst` and the common directives are in [references/rst-cheatsheet.md](references/rst-cheatsheet.md).
