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
