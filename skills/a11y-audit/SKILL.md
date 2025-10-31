---
name: a11y-audit
description: Audit TYPO3 Fluid output (templates, partials, layouts, Fluid components, content elements) and backend module templates for WCAG 2.2 level AA, delegating the template review to the fluid-a11y-reviewer agent and optionally checking rendered pages. Use when the user asks for an accessibility check, a11y review, WCAG or BITV compliance, or before releasing frontend templates.
argument-hint: "[path to templates or URL]"
allowed-tools: Read, Grep, Glob, Bash, Agent
---

# Accessibility audit (WCAG 2.2 AA)

Static template review finds most structural problems; contrast, screen reader output and focus order need a rendered page. This skill does both where possible and is honest about what it could not check.

Target: $ARGUMENTS (default: `Resources/Private/` of the current extension)

## When to use

- New or changed Fluid templates, components or content elements.
- A backend module with custom templates.
- Before a release of a site package or a public extension.
- When an agency or public-sector client asks for WCAG 2.2 AA or BITV 2.0 evidence.

## Steps

1. **Inventory.** List the templates to audit:

    ```bash
    find Resources/Private -type f \( -name '*.html' -o -name '*.fluid.html' \) | sort
    ```

    Group them by folder (Layouts, Templates, Partials, Components, Backend).

2. **Template review.** Delegate to `fluid-a11y-reviewer`, one agent per group when there are more than about 30 templates, all in one message so they run in parallel. Pass the file list and whether it is frontend or backend output.

3. **Rendered check (optional).** If a URL is given or the DDEV site runs (`ddev describe`), check the real output:

    ```bash
    npx --yes @axe-core/cli <url> --tags wcag2a,wcag2aa,wcag21a,wcag21aa,wcag22aa
    ```

    or `npx --yes pa11y --standard WCAG2AA <url>`. Treat automated tools as a floor: they catch roughly a third of issues.

4. **Merge.** Combine agent findings and tool output, remove duplicates by file and criterion, and sort by level (A before AA) then by file.

5. **Report** in the format below. Do not fix templates unless the user asks; then fix A issues first.

## Checklist

The reviewer covers these; use them to sanity-check its reply. Details and Fluid fixes are in [references/wcag-fluid.md](references/wcag-fluid.md).
