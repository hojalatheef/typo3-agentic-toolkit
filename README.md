# TYPO3 Agentic Toolkit

A Claude Code plugin for building TYPO3 v13.4 / v14 extensions and site packages
with the concepts that arrived in the last release cycle — site sets, Fluid
components, the Schema API, record objects, Extbase action authorization and
rate limiting, Symfony translations, the System Resource API, CSP hashes and
more — and with every building block a Claude Code plugin can ship: skills,
subagents, hooks, function hooks, output styles, LSP, MCP, monitors, workflows
and evals.

Every TYPO3 statement in the skills cites the core changelog entry it comes
from (`Feature-108508`, `Feature-104002`, …). Look one up with
`t3-changelog <id>`.

## Install

```text
/plugin marketplace add hojalatheef/typo3-agentic-toolkit
/plugin install typo3-agentic-toolkit@typo3-agentic-toolkit
```

For local development:

```bash
claude --plugin-dir ~/Developer/claude-playground/typo3-agentic-toolkit
```

Requirements: `jq` and `git`. Optional: DDEV or Docker (PHP for hooks and
gates when the host has no working PHP), `intelephense` on `PATH` for the LSP
integration, a checkout of the TYPO3 Dev Companion for the MCP server.
