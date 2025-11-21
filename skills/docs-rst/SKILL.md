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
