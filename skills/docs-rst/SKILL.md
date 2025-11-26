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

## Steps

1. **Set up or migrate.** No `Documentation/`: create the layout above. `Settings.cfg` present: run the migration and review the generated `guides.xml`:

    ```bash
    docker run --rm --pull always -v "$(pwd)":/project ghcr.io/typo3-documentation/render-guides:latest migrate Documentation
    ```

2. **Write.** One topic per page, each with a label (`..  _configuration-site-set:`) so others can link with `:ref:`. Document site-set settings (from `settings.definitions.yaml`, Feature-103437) and extension configuration with `..  confval::`. Take code samples from the real files and add `:caption: EXT:my_ext/<path>`.
3. **Render.**

    ```bash
    docker run --rm --pull always -v "$(pwd)":/project -t ghcr.io/typo3-documentation/render-guides:latest --config=Documentation
    ```

    Output goes to `Documentation-GENERATED-temp/Index.html`. Add that folder to `.gitignore`.

4. **Fix warnings.** Treat every warning as an error: broken `:ref:`, unknown directive, wrong indentation, missing toctree entry. Re-render until clean.
5. **Report** with the format below.

## Checklist

- [ ] `guides.xml` has `project` title, `version` and `release`, and the `extension` element with repository and issue links.
- [ ] Every folder has `Index.rst` with a `toctree`.
- [ ] Every page starts with `..  include:: /Includes.rst.txt` and has a label.
- [ ] Code blocks name their language; captions use `EXT:` paths.
- [ ] Settings are documented with `confval`, including type and default.
- [ ] Render exits 0 with zero warnings.
- [ ] Screenshots have alt text (`:alt:`) and live in `Documentation/Images/`.
